<?php

namespace App\Http\Controllers;

use App\Models\ClosedCall;
use App\Models\QueueSale;
use App\Models\QueueSaleComment;
use App\Models\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QueueSalesController extends Controller
{
    public function index(Request $request)
    {
        // Get the selected date (default to today in New York time)
        $selectedDate = $request->input('date', Carbon::now('America/New_York')->format('Y-m-d'));
        
        // Parse the selected date in New York timezone
        $nyDate = Carbon::parse($selectedDate, 'America/New_York');
        
        // Get start and end of day in New York time
        $startOfDayNY = $nyDate->copy()->startOfDay(); // 00:00:00 NY time
        $endOfDayNY = $nyDate->copy()->endOfDay();     // 23:59:59 NY time
        
        // Convert to UTC for database query
        $startOfDayUTC = $startOfDayNY->copy()->setTimezone('UTC');
        $endOfDayUTC = $endOfDayNY->copy()->setTimezone('UTC');
        
        // Sync closed_calls to queue_sales for the selected date range
        // Query by closed_calls created_at in the selected date range and agent_status = 'Sale made'
        $closedCalls = ClosedCall::whereBetween('created_at', [
            $startOfDayUTC->format('Y-m-d H:i:s'),
            $endOfDayUTC->format('Y-m-d H:i:s')
        ])
        ->where('agent_status', 'Sale made')
        ->orderBy('created_at', 'desc')
        ->get();
        
        foreach ($closedCalls as $call) {
            $status = in_array($call->status, ['pending', 'approved', 'rejected']) ? $call->status : 'pending';
            
            // Use updateOrCreate to sync changes from closed_calls
            QueueSale::updateOrCreate(
                ['closed_call_id' => $call->id],
                [
                    'customer_full_name' => $call->customer_full_name,
                    'state' => $call->state,
                    'carrier' => $call->carrier,
                    'clients_id' => $call->clients_id,
                    'status' => $status,
                    'created_at' => $call->created_at, // Use closed_call's created_at
                    'updated_at' => now(),
                ]
            );
        }
        
        // Get queue sales by filtering on closed_calls.created_at and agent_status = 'Sale made'
        $queueSales = QueueSale::with(['closedCall', 'validator'])
            ->join('closed_calls', 'queue_sales.closed_call_id', '=', 'closed_calls.id')
            ->whereBetween('closed_calls.created_at', [
                $startOfDayUTC->format('Y-m-d H:i:s'),
                $endOfDayUTC->format('Y-m-d H:i:s')
            ])
            ->where('closed_calls.agent_status', 'Sale made')
            ->select('queue_sales.*') // Select only queue_sales columns to avoid conflicts
            ->orderBy('closed_calls.created_at', 'desc')
            ->paginate(20)
            ->appends(['date' => $selectedDate]);
        
        // Get client names from users table
        // Note: Model accessors will handle the timezone formatting automatically
        foreach ($queueSales as $queueSale) {
            // Get client name from users table
            if ($queueSale->clients_id) {
                $client = User::find($queueSale->clients_id);
                $queueSale->client_name = $client ? $client->name : 'N/A';
            } else {
                $queueSale->client_name = 'N/A';
            }
        }
        
        $validators = Validator::all();
        
        // Status counts for the selected date - filter by closed_calls.created_at and agent_status = 'Sale made'
        $statusCounts = [
            'queued' => QueueSale::join('closed_calls', 'queue_sales.closed_call_id', '=', 'closed_calls.id')
                ->where('queue_sales.status', 'pending')
                ->whereNull('queue_sales.clients_id')
                ->whereBetween('closed_calls.created_at', [
                    $startOfDayUTC->format('Y-m-d H:i:s'),
                    $endOfDayUTC->format('Y-m-d H:i:s')
                ])
                ->where('closed_calls.agent_status', 'Sale made')
                ->count(),
            'assigned' => QueueSale::join('closed_calls', 'queue_sales.closed_call_id', '=', 'closed_calls.id')
                ->where('queue_sales.status', 'pending')
                ->whereNotNull('queue_sales.clients_id')
                ->whereBetween('closed_calls.created_at', [
                    $startOfDayUTC->format('Y-m-d H:i:s'),
                    $endOfDayUTC->format('Y-m-d H:i:s')
                ])
                ->where('closed_calls.agent_status', 'Sale made')
                ->count(),
            'approved' => QueueSale::join('closed_calls', 'queue_sales.closed_call_id', '=', 'closed_calls.id')
                ->where('queue_sales.status', 'approved')
                ->whereBetween('closed_calls.created_at', [
                    $startOfDayUTC->format('Y-m-d H:i:s'),
                    $endOfDayUTC->format('Y-m-d H:i:s')
                ])
                ->where('closed_calls.agent_status', 'Sale made')
                ->count(),
            'rejected' => QueueSale::join('closed_calls', 'queue_sales.closed_call_id', '=', 'closed_calls.id')
                ->where('queue_sales.status', 'rejected')
                ->whereBetween('closed_calls.created_at', [
                    $startOfDayUTC->format('Y-m-d H:i:s'),
                    $endOfDayUTC->format('Y-m-d H:i:s')
                ])
                ->where('closed_calls.agent_status', 'Sale made')
                ->count(),
        ];
        
        return view('queue-sales.index', compact('queueSales', 'statusCounts', 'validators', 'selectedDate'));
    }

    public function updateInline(Request $request, $id)
    {
        try {
            $queueSale = QueueSale::findOrFail($id);
            
            $request->validate([
                'validator_id' => 'required|exists:validators,id',
                'status' => 'required|in:pending,approved,rejected',
            ]);

            $data = [
                'validator_id' => $request->validator_id,
                'status' => $request->status,
            ];

            if ($queueSale->validator_id != $request->validator_id) {
                $data['validator_updated_at'] = now();
            }

            if ($queueSale->status != $request->status) {
                $data['status_updated_at'] = now();
            }

            $queueSale->update($data);
            $queueSale->load('validator');
            
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
                'data' => [
                    'id' => $queueSale->id,
                    'validator' => $queueSale->validator,
                    'status' => $queueSale->status,
                    'validator_updated_at' => $queueSale->validator_updated_at_ny,
                    'status_updated_at' => $queueSale->status_updated_at_ny,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Update inline error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleConnection(Request $request, $id)
    {
        try {
            $queueSale = QueueSale::findOrFail($id);
            
            if ($queueSale->is_connected === 1) {
                // Disconnect: set to 0 but keep the timestamp
                $queueSale->update([
                    'is_connected' => 0,
                    'connected_at' => now() // Update timestamp to show when disconnected
                ]);
                
                return response()->json([
                    'success' => true,
                    'is_connected' => 0,
                    'disconnected_at' => $queueSale->connected_at_ny
                ]);
            } else {
                // Connect: set to 1 with timestamp
                $queueSale->update([
                    'is_connected' => 1,
                    'connected_at' => now()
                ]);
                
                return response()->json([
                    'success' => true,
                    'is_connected' => 1,
                    'connected_at' => $queueSale->connected_at_ny
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Toggle connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle connection: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $queueSale = QueueSale::with(['closedCall', 'validator', 'comments.user', 'comments.replies.user'])
                ->findOrFail($id);
            
            // Get client name
            if ($queueSale->clients_id) {
                $client = User::find($queueSale->clients_id);
                $queueSale->client_name = $client ? $client->name : 'N/A';
            } else {
                $queueSale->client_name = 'N/A';
            }
            
            return response()->json([
                'success' => true,
                'data' => $queueSale
            ]);
        } catch (\Exception $e) {
            Log::error('Show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeComment(Request $request, $id)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
                'parent_id' => 'nullable|exists:queue_sale_comments,id'
            ]);

            $comment = QueueSaleComment::create([
                'queue_sale_id' => $id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'parent_id' => $request->parent_id,
            ]);

            $comment->load('user', 'replies.user');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            Log::error('Store comment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteComment($id)
    {
        try {
            $comment = QueueSaleComment::findOrFail($id);
            
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete comment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment: ' . $e->getMessage()
            ], 500);
        }
    }
}