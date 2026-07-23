<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use App\Models\UserBankDetail;
use App\Http\Requests\UserDetailRequest;
use App\Services\FilesUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserDetailController extends Controller
{
    protected $fileUploadService;

    public function __construct(FilesUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index()
    {
        $user = Auth::user();
        
        $userDetail = UserDetail::with([
            'bankDetails', 
            'cnicFront', 
            'cnicBack'
        ])->where('user_id', $user->id)->first();
        
        return view('user.profile', compact('user', 'userDetail'));
    }

    public function saveUserDetails(UserDetailRequest $request)
    {
        try {
            $user = Auth::user();
            
            // Get validated data from request
            $validatedData = $request->getValidatedData();
            $bankDetails = $request->getBankDetails();
            
            // Single function to handle both create and update
            $userDetail = UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                $validatedData
            );

            Log::info('User details saved', [
                'user_id' => $user->id, 
                'action' => $userDetail->wasRecentlyCreated ? 'created' : 'updated'
            ]);

            // Handle file uploads
            $filesToUpload = [];

            if ($request->hasFile('cnic_front')) {
                $filesToUpload[] = [
                    'file' => $request->file('cnic_front'),
                    'category' => 'cnic_front'
                ];
            }

            if ($request->hasFile('cnic_back')) {
                $filesToUpload[] = [
                    'file' => $request->file('cnic_back'),
                    'category' => 'cnic_back'
                ];
            }

            // Upload files if any
            if (!empty($filesToUpload)) {
                $uploadResult = $this->fileUploadService->uploadMultipleFiles(
                    $filesToUpload,
                    $user->id,
                    'App\Models\UserDetail',
                    $userDetail->id
                );

                if (!$uploadResult['success']) {
                    $errorMessages = implode(', ', $uploadResult['errors']);
                    throw new \Exception("File upload verification failed: " . $errorMessages);
                }
            }

            // Save bank details using validated data
            UserBankDetail::where('user_id', $user->id)->delete();
            
            if (!empty($bankDetails['bank_names'])) {
                foreach ($bankDetails['bank_names'] as $index => $bankName) {
                    if (!empty($bankName)) {
                        UserBankDetail::create([
                            'user_id' => $user->id,
                            'bank_name' => $bankName,
                            'account_title' => $bankDetails['account_titles'][$index] ?? '',
                            'account_number' => $bankDetails['account_numbers'][$index] ?? '',
                            'cnic_number' => $bankDetails['bank_cnic_numbers'][$index] ?? '',
                            'priority' => $index + 1,
                            'status' => 'unverified'
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => 'Details ' . ($userDetail->wasRecentlyCreated ? 'saved' : 'updated') . ' successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving user details: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}