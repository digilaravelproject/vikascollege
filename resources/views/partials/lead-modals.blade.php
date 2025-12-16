{{-- ==================== TOAST NOTIFICATION (Top Right) ==================== --}}
<div class="fixed top-5 right-5 z-[100] space-y-2 pointer-events-none" x-cloak>
    <template x-if="toast.show">
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-10"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-10"
             class="pointer-events-auto flex items-center p-4 mb-4 text-white rounded-lg shadow-lg min-w-[300px]"
             :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'">
            
            <div class="mr-3">
                <template x-if="toast.type === 'success'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
            </div>
            <div class="text-sm font-medium" x-text="toast.message"></div>
            <button @click="toast.show = false" class="ml-auto text-white hover:text-gray-200 focus:outline-none"><span class="text-xl">&times;</span></button>
        </div>
    </template>
</div>

{{-- ==================== ADMISSION MODAL ==================== --}}
<div x-show="applyOpen" x-cloak x-transition.opacity
    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/70 p-4">

    <div x-show="applyOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        @click.away="closeApply"
        class="bg-white w-full max-w-xl rounded-xl shadow-2xl transition-all duration-300 overflow-hidden relative">

        {{-- Close Button --}}
        <button @click="closeApply" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl z-10">&times;</button>

        {{-- STATE 1: THE FORM --}}
        <div x-show="!formSuccess">
            <div class="px-6 pt-6 pb-2 border-b border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800">Admission 2025</h2>
                <p class="text-sm text-gray-500">Apply Now for the upcoming session.</p>
            </div>

            <div class="p-6 max-h-[70vh] overflow-y-auto space-y-5">
                <form @submit.prevent="submitAdmission" class="space-y-4">
                    <div class="flex gap-4">
                        <input x-model="ad.first_name" type="text" placeholder="First Name" class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required />
                        <input x-model="ad.last_name" type="text" placeholder="Last Name" class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required />
                    </div>

                    <div class="flex gap-4 items-center">
                        <input x-model="ad.email" type="email" placeholder="Email Address" class="w-3/4 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" :disabled="otpVerified" required />
                        <button type="button" @click="sendOtp('admission', ad.email, ad.first_name)" :disabled="otpSending || otpVerified"
                            class="w-1/4 py-3 text-sm font-semibold rounded-lg transition flex justify-center items-center"
                            :class="otpVerified ? 'bg-green-100 text-green-700 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700'">
                            <span x-show="!otpSending && !otpVerified">Verify</span>
                            <svg x-show="otpSending" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="otpVerified">Verified ✓</span>
                        </button>
                    </div>

                    {{-- OTP Section --}}
                    <div x-show="otpSent && otpFor=='admission' && !otpVerified" x-collapse class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex justify-between mb-2 items-center">
                            <span class="text-xs font-bold text-blue-600 uppercase">Enter OTP</span>
                            {{-- TIMER --}}
                            <div class="flex items-center text-red-600 bg-red-50 px-2 py-1 rounded">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-xs font-mono font-bold" x-text="formatTime(timer)"></span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <input x-model="enteredOtp" type="text" maxlength="6" placeholder="000000" class="flex-1 p-2 border border-blue-300 rounded text-center font-bold tracking-widest text-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            <button type="button" @click="verifyOtp('admission', ad.email)" class="px-4 bg-green-600 text-white font-semibold rounded hover:bg-green-700 transition">Verify</button>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">OTP sent to your email. Check spam if not received.</p>
                    </div>

                    <div class="flex gap-4">
                        <select x-model="ad.mobile_prefix" class="w-1/4 p-3 border border-gray-300 rounded-lg bg-white"><option value="+91">+91</option><option value="+1">+1</option></select>
                        <input x-model="ad.mobile_no" type="text" placeholder="Mobile Number" class="w-3/4 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <select x-model="ad.discipline" class="p-3 border border-gray-300 rounded-lg bg-white"><option value="">Discipline</option><option value="Engineering">Engineering</option></select>
                        <select x-model="ad.level" class="p-3 border border-gray-300 rounded-lg bg-white"><option value="">Level</option><option value="UG">UG</option></select>
                        <select x-model="ad.programme" class="p-3 border border-gray-300 rounded-lg bg-white"><option value="">Programme</option><option value="CS">CS</option></select>
                    </div>

                    <div class="flex items-start gap-3 pt-2">
                        <input type="checkbox" x-model="ad.authorised_contact" id="ad_auth" class="mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                        <label for="ad_auth" class="text-sm text-gray-600 leading-tight">I authorise representative of Somaiya Vidyavihar University to contact me.</label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" :disabled="!otpVerified || sending" class="w-full py-3 text-lg font-semibold rounded-lg transition" :class="!otpVerified || sending ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-red-700 text-white hover:bg-red-800'">
                            <span x-show="!sending">Register Now</span>
                            <span x-show="sending">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- STATE 2: SUCCESS MESSAGE --}}
        <div x-show="formSuccess" class="p-10 flex flex-col items-center justify-center text-center" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-6 animate-bounce">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Successfully Submitted!</h3>
            <p class="text-gray-600 mb-6">Thank you for applying. We have sent a confirmation email to you.</p>
            <button @click="location.reload()" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition">Close</button>
        </div>

    </div>
</div>

{{-- ==================== ENQUIRY MODAL ==================== --}}
<div x-show="enquireOpen" x-cloak x-transition.opacity
    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/70 p-4">

    <div x-show="enquireOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        @click.away="closeEnquire"
        class="bg-white w-full max-w-xl rounded-xl shadow-2xl transition-all duration-300 overflow-hidden relative">

        <button @click="closeEnquire" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl z-10">&times;</button>

        {{-- STATE 1: THE FORM --}}
        <div x-show="!formSuccess">
            <div class="px-6 pt-6 pb-2 border-b border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800">General Enquiry</h2>
                <p class="text-sm text-gray-500">We're happy to answer your questions.</p>
            </div>

            <div class="p-6 max-h-[70vh] overflow-y-auto space-y-5">
                <form @submit.prevent="submitEnquiry" class="space-y-4">
                    <div class="flex gap-4">
                        <input x-model="en.first_name" type="text" placeholder="First Name" class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required />
                        <input x-model="en.last_name" type="text" placeholder="Last Name" class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required />
                    </div>

                    <div class="flex gap-4 items-center">
                        <input x-model="en.email" type="email" placeholder="Email Address" class="w-3/4 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" :disabled="otpVerified" required />
                        <button type="button" @click="sendOtp('enquiry', en.email, en.first_name)" :disabled="otpSending || otpVerified"
                            class="w-1/4 py-3 text-sm font-semibold rounded-lg transition flex justify-center items-center"
                            :class="otpVerified ? 'bg-green-100 text-green-700 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700'">
                            <span x-show="!otpSending && !otpVerified">Verify</span>
                            <svg x-show="otpSending" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="otpVerified">Verified ✓</span>
                        </button>
                    </div>

                    {{-- OTP Section --}}
                    <div x-show="otpSent && otpFor=='enquiry' && !otpVerified" x-collapse class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex justify-between mb-2 items-center">
                            <span class="text-xs font-bold text-blue-600 uppercase">Enter OTP</span>
                            {{-- TIMER --}}
                            <div class="flex items-center text-red-600 bg-red-50 px-2 py-1 rounded">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-xs font-mono font-bold" x-text="formatTime(timer)"></span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <input x-model="enteredOtp" type="text" maxlength="6" placeholder="000000" class="flex-1 p-2 border border-blue-300 rounded text-center font-bold tracking-widest text-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                            <button type="button" @click="verifyOtp('enquiry', en.email)" class="px-4 bg-green-600 text-white font-semibold rounded hover:bg-green-700 transition">Verify</button>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <select x-model="en.mobile_prefix" class="w-1/4 p-3 border border-gray-300 rounded-lg bg-white"><option value="+91">+91</option><option value="+1">+1</option></select>
                        <input x-model="en.mobile_no" type="text" placeholder="Mobile Number" class="w-3/4 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required />
                    </div>

                    <textarea x-model="en.message" rows="4" placeholder="Your question or message..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>

                    <div class="flex items-start gap-3 pt-2">
                        <input type="checkbox" x-model="en.authorised_contact" id="en_auth" class="mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                        <label for="en_auth" class="text-sm text-gray-600 leading-tight">I authorise representative of Somaiya Vidyavihar University to contact me.</label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" :disabled="!otpVerified || sending" class="w-full py-3 text-lg font-semibold rounded-lg transition" :class="!otpVerified || sending ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-red-700 text-white hover:bg-red-800'">
                            <span x-show="!sending">Submit Enquiry</span>
                            <span x-show="sending">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- STATE 2: SUCCESS MESSAGE --}}
        <div x-show="formSuccess" class="p-10 flex flex-col items-center justify-center text-center" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-6 animate-bounce">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Enquiry Sent!</h3>
            <p class="text-gray-600 mb-6">We have received your message and will get back to you soon.</p>
            <button @click="location.reload()" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition">Close</button>
        </div>

    </div>
</div>

{{-- ==================== JAVASCRIPT LOGIC (UPDATED) ==================== --}}
<script>
    // Wait for Alpine to initialize
    document.addEventListener('alpine:init', () => {
        Alpine.data('leadForms', () => ({
            // Modal Visibility
            applyOpen: false,
            enquireOpen: false,
            
            // Helper methods for Sticky Buttons
            openApply() { this.applyOpen = true; },
            openEnquire() { this.enquireOpen = true; },

            // UI Logic
            otpSending: false,
            otpSent: false,
            otpVerified: false,
            otpFor: '', 
            sending: false,
            formSuccess: false, // New State for Success Screen
            enteredOtp: '',
            
            // Timer logic
            timer: 600, // 10 minutes
            interval: null,

            // Toast
            toast: { show: false, type: 'success', message: '' },

            // Data
            ad: { first_name: '', last_name: '', email: '', mobile_prefix: '+91', mobile_no: '', discipline: '', level: '', programme: '', authorised_contact: false },
            en: { first_name: '', last_name: '', email: '', mobile_prefix: '+91', mobile_no: '', level: '', discipline: '', programme: '', message: '', authorised_contact: false },

            // Notifications (Top Right)
            notify(type, message) {
                this.toast.type = type;
                this.toast.message = message;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false }, 5000);
            },

            // Timer Function
            startTimer() {
                this.timer = 600; 
                if (this.interval) clearInterval(this.interval);
                this.interval = setInterval(() => {
                    if (this.timer > 0) {
                        this.timer--;
                    } else {
                        clearInterval(this.interval);
                        this.notify('error', 'OTP Expired. Please resend.');
                        this.otpSent = false; 
                    }
                }, 1000);
            },

            formatTime(seconds) {
                const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                const s = (seconds % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            },

            // Reset everything when closing
            resetForms() {
                this.otpSent = false;
                this.otpVerified = false;
                this.formSuccess = false; // Reset success screen
                this.enteredOtp = '';
                this.otpFor = '';
                if (this.interval) clearInterval(this.interval);
            },

            closeApply() { this.applyOpen = false; this.resetForms(); },
            closeEnquire() { this.enquireOpen = false; this.resetForms(); },

            // --- API Actions ---

            async sendOtp(type, email, name) {
                if (!email) { this.notify('error', 'Please enter a valid email address.'); return; }
                this.otpSending = true;
                
                try {
                    let response = await fetch("{{ route('send.otp') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ email: email, name: name, type: type })
                    });
                    let result = await response.json();
                    if (response.ok && result.ok) {
                        this.notify('success', 'OTP sent! Please check your email.');
                        this.otpSent = true;
                        this.otpFor = type;
                        this.startTimer();
                    } else { throw new Error(result.message || 'Failed to send OTP'); }
                } catch (error) { this.notify('error', error.message); } finally { this.otpSending = false; }
            },

            async verifyOtp(type, email) {
                if (!this.enteredOtp || this.enteredOtp.length !== 6) { this.notify('error', 'Enter a 6-digit OTP.'); return; }
                try {
                    let response = await fetch("{{ route('verify.otp') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ email: email, otp: this.enteredOtp, type: type })
                    });
                    let result = await response.json();
                    if (response.ok && result.ok) {
                        this.notify('success', 'Email Verified Successfully!');
                        this.otpVerified = true;
                        if (this.interval) clearInterval(this.interval);
                    } else { this.notify('error', result.message || 'Invalid OTP'); }
                } catch (error) { this.notify('error', 'Verification failed.'); }
            },

            async submitAdmission() {
                if (!this.ad.authorised_contact) { this.notify('error', 'Please authorize us to contact you.'); return; }
                this.sending = true;
                try {
                    let response = await fetch("{{ route('submit.admission') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.ad)
                    });
                    let result = await response.json();
                    if (response.ok && result.ok) {
                         // SUCCESS LOGIC: Switch UI to Success Screen
                         this.formSuccess = true; 
                         // No alert() here!
                    } else {
                        let msg = result.message;
                        if(result.errors) { msg = Object.values(result.errors).flat().join(' '); }
                        throw new Error(msg);
                    }
                } catch (error) { this.notify('error', error.message); } finally { this.sending = false; }
            },

            async submitEnquiry() {
                if (!this.en.authorised_contact) { this.notify('error', 'Please authorize us to contact you.'); return; }
                this.sending = true;
                try {
                    let response = await fetch("{{ route('submit.enquiry') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.en)
                    });
                    let result = await response.json();
                    if (response.ok && result.ok) {
                          // SUCCESS LOGIC: Switch UI to Success Screen
                          this.formSuccess = true; 
                    } else {
                        let msg = result.message;
                        if(result.errors) { msg = Object.values(result.errors).flat().join(' '); }
                        throw new Error(msg);
                    }
                } catch (error) { this.notify('error', error.message); } finally { this.sending = false; }
            }
        }));
    });
</script>