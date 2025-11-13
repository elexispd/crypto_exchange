<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use App\Http\Requests\StoreKycDocumentRequest;
use App\Http\Requests\UpdateKycDocumentRequest;
use App\Http\Resources\KycDocumentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;

class KycDocumentController extends Controller
{
    /**
     * Display a listing of the KYC documents for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $kycDocuments = $request->user()->kycDocuments()->latest()->get();

        return response()->json([
            'status' => true,
            'data' => KycDocumentResource::collection($kycDocuments),
            'message' => 'KYC documents retrieved successfully.'
        ]);
    }

    /**
     * Store a newly created KYC document in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'front_image'   => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'back_image'    => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        // Check if user already has a pending or approved KYC
        $existingKyc = $request->user()->kycDocument()->whereIn('status', ['pending', 'approved'])->first();

        if ($existingKyc) {
            return response()->json([
                'status' => false,
                'message' => 'You already have a KYC submission that is pending or approved.'
            ], 422);
        }

        // Handle front image upload
        $frontFile = $request->file('front_image');
        $frontFilename = uniqid() . '_' . $frontFile->getClientOriginalName();
        $frontPath = $frontFile->move(storage_path('app/public/kyc/documents'), $frontFilename);

        // Convert absolute path to relative path for database storage
        $frontImagePath = 'kyc/documents/' . $frontFilename;

        // Handle back image upload if exists
        $backImagePath = null;
        if ($request->hasFile('back_image')) {
            $backFile = $request->file('back_image');
            $backFilename = uniqid() . '_' . $backFile->getClientOriginalName();
            $backPath = $backFile->move(storage_path('app/public/kyc/documents'), $backFilename);
            $backImagePath = 'kyc/documents/' . $backFilename;
        }

        $kycDocument = KycDocument::create([
            'user_id' => $request->user()->id,
            'document_type' => $validated['document_type'],
            'front_image' => $frontImagePath,
            'back_image' => $backImagePath,
        ]);

        // Send KYC submitted notification
        NotificationService::kycSubmitted($kycDocument);

        return response()->json([
            'status' => true,
            'data' => new KycDocumentResource($kycDocument),
            'message' => 'KYC document submitted successfully. Waiting for admin approval.'
        ], 201);
    }
    /**
     * Display the specified KYC document.
     */
    public function show(Request $request, KycDocument $kyc): JsonResponse
    {
        // Ensure user can only view their own KYC documents
        if ($request->user()->id !== $kyc->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized to view this KYC document.'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => new KycDocumentResource($kyc),
            'message' => 'KYC document retrieved successfully.'
        ]);
    }

    /**
     * Update the specified KYC document in storage.
     */
    public function update(Request $request, KycDocument $kyc): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'front_image'   => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'back_image'    => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        // Ensure user can only update their own KYC documents
        if ($request->user()->id !== $kyc->user_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized to update this KYC document.'
            ], 403);
        }

        // Cannot update if already approved or rejected
        if ($kyc->status !== 'pending') {
            return response()->json([
                'status'  => false,
                'message' => 'Cannot update KYC document that has already been processed.'
            ], 422);
        }

        // Prepare data for update
        $data = [
            'document_type' => $validated['document_type'],
        ];

        // Handle front image upload
        if ($request->hasFile('front_image')) {
            if ($kyc->front_image) {
                Storage::disk('public')->delete($kyc->front_image);
            }

            $frontFile = $request->file('front_image');
            $frontFilename = uniqid() . '_' . $frontFile->getClientOriginalName();
            $frontFile->move(storage_path('app/public/kyc/documents'), $frontFilename);

            $data['front_image'] = 'kyc/documents/' . $frontFilename;
        }

        // Handle back image upload
        if ($request->hasFile('back_image')) {
            if ($kyc->back_image) {
                Storage::disk('public')->delete($kyc->back_image);
            }

            $backFile = $request->file('back_image');
            $backFilename = uniqid() . '_' . $backFile->getClientOriginalName();
            $backFile->move(storage_path('app/public/kyc/documents'), $backFilename);

            $data['back_image'] = 'kyc/documents/' . $backFilename;
        }

        // Get the original status before update
        $originalStatus = $kyc->status;
        $rejectionReason = $request->input('rejection_reason');

        // Perform update
        $kyc->update($data);
        
        // Refresh the model to get the latest data
        $kyc->refresh();

        // Check if this is an admin updating the status
        if ($request->user()->isAdmin()) {
            // If status was changed to approved
            if ($kyc->status === 'approved' && $originalStatus !== 'approved') {
                $kyc->update(['verified_at' => now()]);
                NotificationService::kycApproved($kyc);
            }
            // If status was changed to rejected
            elseif ($kyc->status === 'rejected' && $originalStatus !== 'rejected' && $rejectionReason) {
                $kyc->update(['rejection_reason' => $rejectionReason]);
                NotificationService::kycRejected($kyc, $rejectionReason);
            }
        }

        return response()->json([
            'status'  => true,
            'data'    => new KycDocumentResource($kyc->fresh()),
            'message' => 'KYC document updated successfully.'
        ]);
    }


    /**
     * Get KYC status for the authenticated user.
     */
    public function status(Request $request): JsonResponse
    {
        $kycDocument = $request->user()->kycDocument()->latest()->first();

        return response()->json([
            'status' => $kycDocument ? $kycDocument->status : 'not_submitted',
            'submitted_at' => $kycDocument ? $kycDocument->created_at : null,
            'verified_at' => $kycDocument ? $kycDocument->verified_at : null,
            'rejection_reason' => $kycDocument ? $kycDocument->rejection_reason : null,
            'message' => 'KYC status retrieved successfully.'
        ]);
    }
}
