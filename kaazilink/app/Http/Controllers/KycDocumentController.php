<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\KycSubmission;
use Illuminate\Support\Facades\Auth;

class KycDocumentController extends Controller
{
    public function show($path)
    {
        $user = Auth::user();

        // Admin can view any document
        if ($user && $user->role === 'admin') {
            return $this->serveFile($path);
        }

        // Provider can view their own documents
        if ($user) {
            $isOwner = KycSubmission::where('user_id', $user->id)
                ->where(function ($query) use ($path) {
                    $query->where('id_front_path', $path)
                          ->orWhere('id_back_path', $path)
                          ->orWhere('kra_pin_path', $path);
                })->exists();
                
            if ($isOwner) {
                return $this->serveFile($path);
            }
        }

        abort(403, 'Unauthorized access to KYC document.');
    }

    private function serveFile($path)
    {
        if (!Storage::disk('kyc')->exists($path)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('kyc')->response($path);
    }
}
