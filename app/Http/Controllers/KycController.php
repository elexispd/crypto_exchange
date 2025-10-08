<?php

namespace App\Http\Controllers;

use App\Http\Resources\KycDocumentResource;
use App\Models\KycDocument;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $kycs = KycDocument::query()
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('kyc_documents')
                    ->groupBy('user_id');
            })
            ->with('user')
            ->latest()
            ->get();

        return view('kyc.index', compact('kycs', 'status'));
    }


    public function update(Request $request, KycDocument $kyc)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|nullable|max:500',
        ]);

        if ($request->action === 'approve') {
            $kyc->update([
                'status' => 'verified',
                'rejection_reason' => null,
                'verified_at' => now(),
            ]);
        } else {
            $kyc->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'verified_at' => null,
            ]);
        }

        return back()->with('success', "KYC {$request->action}d successfully.");
    }


    public function destroy(KycDocument $kyc)
    {
        $kyc->delete();
        return back()->with('success', 'KYC deleted successfully.');
    }
}
