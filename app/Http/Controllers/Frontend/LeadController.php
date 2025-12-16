<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Mail\AdmissionMailToAdmin;
use App\Mail\AdmissionMailToStudent;
use App\Mail\EnquiryMailToAdmin;
use App\Mail\EnquiryMailToStudent;
use App\Models\Admission;
use App\Models\Enquiry;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    // --- Send OTP ---
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $otp = random_int(100000, 999999);

        $key = 'otp:' . $email . ':' . $request->type;
        Cache::put($key, $otp, now()->addMinutes(10));

        try {
            // 1. Apply Config
            MailConfigService::applyFromDb();

            // 2. Send Mail (Sync)
            Mail::to($email)->send(new SendOtpMail($otp, $request->name ?? null));

            return response()->json(['ok' => true, 'message' => 'OTP sent successfully']);

        } catch (\Exception $e) {
            Log::error("OTP Send Error: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }

    // --- Verify OTP ---
    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|digits:6', 'type' => 'required|string']);
        $key = 'otp:' . $request->email . ':' . $request->type;
        $cached = Cache::get($key);

        if (!$cached || (string)$cached !== (string)$request->otp) {
            return response()->json(['ok' => false, 'message' => 'Invalid OTP'], 422);
        }

        Cache::put('verified:' . $request->email . ':' . $request->type, true, now()->addMinutes(10));
        Cache::forget($key);

        return response()->json(['ok' => true]);
    }

    // --- Submit Admission ---
    public function submitAdmission(Request $request)
    {
        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'mobile_prefix' => 'nullable|string|max:10',
            'mobile_no' => 'nullable|string|max:20',
            'discipline' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:255',
            'programme' => 'nullable|string|max:255',
            'authorised_contact' => 'nullable|boolean',
        ]);

        if (!cache('verified:' . $request->email . ':admission')) {
            return response()->json(['ok' => false, 'message' => 'Email not verified'], 422);
        }

        $data = $request->only(['first_name', 'last_name', 'email', 'mobile_prefix', 'mobile_no', 'discipline', 'level', 'programme']);
        $data['authorised_contact'] = (bool) $request->authorised_contact;

        // 1. Save Data First
        $admission = Admission::create($data + ['status' => 'submitted', 'verified_at' => now()]);
        
        // Clear token immediately
        Cache::forget('verified:' . $request->email . ':admission');

        // 2. Send Emails (Wrapped in Try-Catch so DB entry persists even if mail fails)
        try {
            MailConfigService::applyFromDb();
            $adminMail = MailConfigService::getReceivingEmail();

            // Send to Admin
            if ($adminMail) {
                Mail::to($adminMail)->send(new AdmissionMailToAdmin($admission));
            }
            
            // Send to Student
            Mail::to($admission->email)->send(new AdmissionMailToStudent($admission));

        } catch (\Exception $e) {
            // Log error but don't stop the user flow since data is saved
            Log::error("Admission Mail Failed: " . $e->getMessage());
        }

        return response()->json(['ok' => true, 'message' => 'Application submitted successfully']);
    }

    // --- Submit Enquiry ---
    public function submitEnquiry(Request $request)
    {
        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'mobile_prefix' => 'nullable|string|max:10',
            'mobile_no' => 'nullable|string|max:20',
            'level' => 'nullable|string|max:255',
            'discipline' => 'nullable|string|max:255',
            'programme' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'authorised_contact' => 'nullable|boolean',
        ]);

        if (!cache('verified:' . $request->email . ':enquiry')) {
            return response()->json(['ok' => false, 'message' => 'Email not verified'], 422);
        }

        $data = $request->only(['first_name', 'last_name', 'email', 'mobile_prefix', 'mobile_no', 'level', 'discipline', 'programme', 'message']);
        $data['authorised_contact'] = (bool) $request->authorised_contact;

        // 1. Save Data
        $enquiry = Enquiry::create($data + ['status' => 'submitted', 'verified_at' => now()]);

        Cache::forget('verified:' . $request->email . ':enquiry');

        // 2. Send Emails
        try {
            MailConfigService::applyFromDb();
            $adminMail = MailConfigService::getReceivingEmail();

            // Send to Admin
            if ($adminMail) {
                Mail::to($adminMail)->send(new EnquiryMailToAdmin($enquiry));
            }

            // Send to Student
            Mail::to($enquiry->email)->send(new EnquiryMailToStudent($enquiry));

        } catch (\Exception $e) {
            Log::error("Enquiry Mail Failed: " . $e->getMessage());
        }

        return response()->json(['ok' => true, 'message' => 'Enquiry submitted successfully']);
    }
}