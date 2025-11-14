<div x-show="applyOpen" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/70 p-4">

    <div x-show="applyOpen" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        @click.away="closeApply"
        class="bg-white w-full max-w-xl rounded-xl shadow-2xl transition-all duration-300 overflow-hidden">

        {{-- **NEW: Clean Header/Title Area** --}}
        <div class="px-6 pt-6 pb-2 border-b border-gray-100 relative">
            <h2 class="text-2xl font-bold text-gray-800">Admission 2025</h2>
            <p class="text-sm text-gray-500">Apply Now for the upcoming session.</p>
            <button @click="closeApply"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-xl leading-none transition">&times;</button>
        </div>

        <div class="p-6 max-h-[70vh] overflow-y-auto space-y-5">
            <form @submit.prevent="submitAdmission" class="space-y-4">
                {{-- Row 1: Name Fields --}}
                <div class="flex gap-4">
                    <input x-model="ad.first_name" type="text" placeholder="First Name"
                        class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    <input x-model="ad.last_name" type="text" placeholder="Last Name"
                        class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>

                {{-- Row 2: Email & Verify Button --}}
                <div class="flex gap-4 items-center">
                    <input x-model="ad.email" type="email" placeholder="Email Address"
                        class="w-3/4 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    <button type="button" @click="sendOtp('admission')" :disabled="otpSending || otpVerified"
                        class="w-1/4 py-3 text-sm font-semibold rounded-lg transition"
                        :class="otpVerified ? 'bg-green-100 text-green-700 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700'">
                        <span x-show="!otpSending && !otpVerified">Verify</span>
                        <span x-show="otpSending">Sending...</span>
                        <span x-show="otpVerified">Verified!</span>
                    </button>
                </div>

                {{-- Row 3: OTP Input --}}
                <div x-show="otpSent && otpFor=='admission' && !otpVerified"
                    class="flex gap-4 items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <input x-model="enteredOtp" type="text" placeholder="Enter OTP (Check Email)"
                        class="p-2 border border-blue-300 rounded-lg w-1/2 focus:ring-blue-500 focus:border-blue-500" />
                    <button type="button" @click="verifyOtp('admission')"
                        class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">Verify
                        Code</button>
                </div>

                {{-- Row 4: Mobile Number --}}
                <div class="flex gap-4">
                    <select x-model="ad.mobile_prefix"
                        class="w-1/4 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="+91">+91</option>
                        <option value="+1">+1</option>
                    </select>
                    <input x-model="ad.mobile_no" type="text" placeholder="Mobile Number"
                        class="w-3/4 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>

                {{-- Row 5: Course Details (Use flex-1 for better spacing) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <select x-model="ad.discipline"
                        class="p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Discipline</option>
                        {{-- Add actual discipline options here --}}
                    </select>
                    <select x-model="ad.level"
                        class="p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Level</option>
                        {{-- Add actual level options here --}}
                    </select>
                    <select x-model="ad.programme"
                        class="p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Programme</option>
                        {{-- Add actual programme options here --}}
                    </select>
                </div>

                {{-- Row 6: Checkbox --}}
                <div class="flex items-start gap-3 pt-2">
                    <input type="checkbox" x-model="ad.authorised_contact" id="ad_auth"
                        class="mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                    <label for="ad_auth" class="text-sm text-gray-600 leading-tight">
                        I authorise representative of Somaiya Vidyavihar University to contact me.
                    </label>
                </div>

                {{-- Row 7: Register Button (Bottom) --}}
                <div class="pt-4 flex justify-end">
                    <button type="submit" :disabled="!otpVerified || sending"
                        class="w-full py-3 text-lg font-semibold rounded-lg transition"
                        :class="!otpVerified || sending ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-red-700 text-white hover:bg-red-800'">
                        <span x-show="!sending">Register</span>
                        <span x-show="sending">Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="enquireOpen" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/70 p-4">

    <div x-show="enquireOpen" x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100" @click.away="closeEnquire"
        class="bg-white w-full max-w-xl rounded-xl shadow-2xl transition-all duration-300 overflow-hidden">

        {{-- **NEW: Clean Header/Title Area** --}}
        <div class="px-6 pt-6 pb-2 border-b border-gray-100 relative">
            <h2 class="text-2xl font-bold text-gray-800">General Enquiry</h2>
            <p class="text-sm text-gray-500">We're happy to answer your questions.</p>
            <button @click="closeEnquire"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-xl leading-none transition">&times;</button>
        </div>

        <div class="p-6 max-h-[70vh] overflow-y-auto space-y-5">
            <form @submit.prevent="submitEnquiry" class="space-y-4">
                {{-- Row 1: Name Fields --}}
                <div class="flex gap-4">
                    <input x-model="en.first_name" type="text" placeholder="First Name"
                        class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    <input x-model="en.last_name" type="text" placeholder="Last Name"
                        class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>

                {{-- Row 2: Email & Verify Button --}}
                <div class="flex gap-4 items-center">
                    <input x-model="en.email" type="email" placeholder="Email Address"
                        class="w-3/4 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    <button type="button" @click="sendOtp('enquiry')" :disabled="otpSending || otpVerified"
                        class="w-1/4 py-3 text-sm font-semibold rounded-lg transition"
                        :class="otpVerified ? 'bg-green-100 text-green-700 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700'">
                        <span x-show="!otpSending && !otpVerified">Verify</span>
                        <span x-show="otpSending">Sending...</span>
                        <span x-show="otpVerified">Verified!</span>
                    </button>
                </div>

                {{-- Row 3: OTP Input --}}
                <div x-show="otpSent && otpFor=='enquiry' && !otpVerified"
                    class="flex gap-4 items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <input x-model="enteredOtp" type="text" placeholder="Enter OTP (Check Email)"
                        class="p-2 border border-blue-300 rounded-lg w-1/2 focus:ring-blue-500 focus:border-blue-500" />
                    <button type="button" @click="verifyOtp('enquiry')"
                        class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">Verify
                        Code</button>
                </div>

                {{-- Row 4: Mobile Number --}}
                <div class="flex gap-4">
                    <select x-model="en.mobile_prefix"
                        class="w-1/4 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="+91">+91</option>
                        <option value="+1">+1</option>
                    </select>
                    <input x-model="en.mobile_no" type="text" placeholder="Mobile Number"
                        class="w-3/4 p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>

                {{-- Row 5: Message Area --}}
                <textarea x-model="en.message" rows="4" placeholder="Your question or message..."
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>

                {{-- Row 6: Checkbox --}}
                <div class="flex items-start gap-3 pt-2">
                    <input type="checkbox" x-model="en.authorised_contact" id="en_auth"
                        class="mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500" />
                    <label for="en_auth" class="text-sm text-gray-600 leading-tight">
                        I authorise representative of Somaiya Vidyavihar University to contact me.
                    </label>
                </div>

                {{-- Row 7: Register Button (Bottom) --}}
                <div class="pt-4 flex justify-end">
                    <button type="submit" :disabled="!otpVerified || sending"
                        class="w-full py-3 text-lg font-semibold rounded-lg transition"
                        :class="!otpVerified || sending ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-red-700 text-white hover:bg-red-800'">
                        <span x-show="!sending">Submit Enquiry</span>
                        <span x-show="sending">Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
