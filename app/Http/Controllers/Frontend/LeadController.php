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
use Illuminate\Support\Str;

class LeadController extends Controller
{
    // send OTP (for both admission and enquiry)
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $otp = random_int(100000, 999999);

        // store in cache for 10 minutes keyed by email+type
        $key = 'otp:' . $email . ':' . $request->type;
        Cache::put($key, $otp, now()->addMinutes(10));

        // apply smtp runtime config
        // MailConfigService::applyFromDb(); // <-- REMOVED: Now handled by SendOtpMail Mailable

        Mail::to($email)->queue(new SendOtpMail($otp, $request->name ?? null));

        return response()->json(['ok' => true, 'message' => 'OTP sent']);
    }

    // verify otp
    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|digits:6', 'type' => 'required|string']);
        $key = 'otp:' . $request->email . ':' . $request->type;
        $cached = Cache::get($key);

        if (!$cached || (string)$cached !== (string)$request->otp) {
            return response()->json(['ok' => false, 'message' => 'Invalid OTP'], 422);
        }

        // mark verified token for this email+type for next 10 minutes
        Cache::put('verified:' . $request->email . ':' . $request->type, true, now()->addMinutes(10));
        Cache::forget($key);

        return response()->json(['ok' => true]);
    }

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

        // check verification
        if (!cache('verified:' . $request->email . ':admission')) {
            return response()->json(['ok' => false, 'message' => 'Email not verified'], 422);
        }

        $data = $request->only(['first_name', 'last_name', 'email', 'mobile_prefix', 'mobile_no', 'discipline', 'level', 'programme']);
        $data['authorised_contact'] = (bool) $request->authorised_contact;

        // NOTE: Keep this here, but ALSO apply fix to all other Mailables
        // MailConfigService::applyFromDb();

        $admission = Admission::create($data + ['status' => 'submitted', 'verified_at' => now()]);

        // send mails queued
        $adminMail = MailConfigService::getReceivingEmail();
        Mail::to($adminMail)->queue(new AdmissionMailToAdmin($admission));
        Mail::to($admission->email)->queue(new AdmissionMailToStudent($admission));

        // clear verification token
        Cache::forget('verified:' . $request->email . ':admission');

        return response()->json(['ok' => true, 'message' => 'Application submitted']);
    }

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

        // NOTE: Keep this here, but ALSO apply fix to all other Mailables
        // MailConfigService::applyFromDb();

        $enquiry = Enquiry::create($data + ['status' => 'submitted', 'verified_at' => now()]);

        $adminMail = MailConfigService::getReceivingEmail();
        Mail::to($adminMail)->queue(new EnquiryMailToAdmin($enquiry));
        Mail::to($enquiry->email)->queue(new EnquiryMailToStudent($enquiry));

        Cache::forget('verified:' . $request->email . ':enquiry');

        return response()->json(['ok' => true, 'message' => 'Enquiry submitted']);
    }
}
