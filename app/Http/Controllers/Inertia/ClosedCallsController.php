<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClosedCall;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClosedCallsController extends Controller
{
    private const PER_PAGE = 20;

    /**
     * Display a listing of closed calls.
     */
    public function index(Request $request): Response
    {
        $authUser = $request->user();

        // 1. Authorization check
        $bypassTypes = ['company', 'super admin'];
        abort_unless(
            $authUser->can('closed call records') || in_array($authUser->type, $bypassTypes, true),
            403,
            'You don\'t have permission to view this page.'
        );

        $search = $request->string('search')->trim()->toString();
        $page = max(1, (int) $request->query('page', 1));

        $query = ClosedCall::query()
            ->orderBy('created_at', 'desc')
            ->with([
                'closer:id,name',
                'client:id,name'
            ])
            ->select([
                'id',
                'created_at',
                'closer_id',
                'closername',
                'status',
                'customer_eligibility',
                'clients_id',
                'carrier',
                'monthly_premium'
            ]);

        // 2. Tenant/Client scoping (matching legacy logic)
        if ($authUser->type === 'client') {
            $authUserEmail = $authUser->email;
            $client = Client::where('email', $authUserEmail)->first();
            
            if ($client) {
                // Parent client: get associated client users
                $associatedUserIds = User::where('type', 'client')
                    ->where('client_id', $client->id)
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($associatedUserIds)) {
                    $query->whereIn('clients_id', $associatedUserIds);
                } else {
                    $query->where('id', 0); // Force empty result
                }
            } else {
                // Child client: match auth user directly
                $query->where('clients_id', $authUser->id);
            }
        }

        // 3. Search Filter
        if ($search !== '') {
            $term = '%' . addcslashes($search, '%_\\') . '%';
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('customer_full_name', 'like', $term)
                    ->orWhere('carrier', 'like', $term)
                    ->orWhere('status', 'like', $term)
                    ->orWhere('closername', 'like', $term)
                    ->orWhere('customer_eligibility', 'like', $term);
            });
        }

        $paginator = $query->paginate(self::PER_PAGE, ['*'], 'page', $page)
            ->withQueryString();

        return Inertia::render('ClosedCalls/Index', [
            'closedCalls' => [
                'data' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
            'filters' => [
                'search' => $search,
            ],
        ]);
    }
}
