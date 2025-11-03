@php
    use App\Models\Menu;
    $topMenus = Menu::where('status', 1)
        ->whereNull('parent_id')
        ->orderBy('order')
        ->limit(8)
        ->get();

    $collegeName = setting('college_name', config('app.name'));
    $logoPath = setting('college_logo');
    $address = setting('address');
    $email = setting('email');
    $phone = setting('phone');
    $facebook = setting('facebook_url');
    $twitter = setting('twitter_url');
    $instagram = setting('instagram_url');
    $youtube = setting('youtube_url');
    $linkedin = setting('linkedin_url');
    $footerAbout = setting('footer_about', 'Committed to academic excellence, innovation, and holistic development.');
    $mapUrl = setting('map_embed_url');
    $footerLinksRaw = setting('footer_links');
    $footerLinks = $footerLinksRaw ? json_decode($footerLinksRaw, true) : [];
@endphp

<footer class="mt-10 bg-[#0b2c3f] text-white">
    <div class="relative">
        <svg aria-hidden="true" focusable="false" class="w-full h-8 text-[#0b2c3f]" preserveAspectRatio="none"
            viewBox="0 0 1440 56">
            <path fill="#0b2c3f" d="M0,24 C240,48 480,0 720,16 C960,32 1200,56 1440,24 L1440,56 L0,56 Z"></path>
        </svg>
    </div>
    <div class="px-4 pt-8 pb-8 mx-auto max-w-7xl">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-5">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    @if ($logoPath)
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $collegeName }}"
                            class="object-contain w-14 h-14 rounded bg-white/10 p-1.5">
                    @else
                        @include('components.application-logo')
                    @endif
                    <div>
                        <div class="text-lg font-semibold">{{ $collegeName }}</div>
                        <div class="text-sm text-gray-300">Shaping futures with excellence</div>
                    </div>
                </div>
                <p class="text-sm leading-relaxed text-gray-300">{{ $footerAbout }}</p>
                @if ($address || $email || $phone)
                    <div class="pt-2 space-y-2 text-sm text-gray-300">
                        @if ($address)
                            <div class="flex gap-2">
                                <span class="mt-0.5" aria-hidden="true">📍</span>
                                <span>{{ $address }}</span>
                            </div>
                        @endif
                        @if ($phone)
                            <div class="flex gap-2">
                                <span class="mt-0.5" aria-hidden="true">📞</span>
                                <a class="hover:text-[#35abe7]"
                                    href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
                            </div>
                        @endif
                        @if ($email)
                            <div class="flex gap-2">
                                <span class="mt-0.5" aria-hidden="true">✉️</span>
                                <a class="hover:text-[#35abe7]" href="mailto:{{ $email }}">{{ $email }}</a>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($facebook || $twitter || $instagram || $youtube || $linkedin)
                    <div class="flex gap-3 pt-2">
                        @if ($facebook)
                            <a href="{{ $facebook }}" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-[#35abe7] transition"
                                aria-label="Facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 3h-1.9v7A10 10 0 0 0 22 12" />
                                </svg>
                            </a>
                        @endif
                        @if ($twitter)
                            <a href="{{ $twitter }}" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-[#35abe7] transition"
                                aria-label="Twitter">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M22.46 6c-.77.35-1.6.58-2.46.69a4.25 4.25 0 0 0 1.86-2.35 8.52 8.52 0 0 1-2.7 1.03 4.24 4.24 0 0 0-7.22 3.87A12.04 12.04 0 0 1 3.1 4.9a4.22 4.22 0 0 0 1.31 5.66 4.2 4.2 0 0 1-1.92-.53v.05a4.24 4.24 0 0 0 3.4 4.16 4.25 4.25 0 0 1-1.91.07 4.24 4.24 0 0 0 3.95 2.93A8.5 8.5 0 0 1 2 19.54a12.02 12.02 0 0 0 6.51 1.91c7.82 0 12.1-6.48 12.1-12.1l-.01-.55A8.64 8.64 0 0 0 22.46 6z" />
                                </svg>
                            </a>
                        @endif
                        @if ($instagram)
                            <a href="{{ $instagram }}" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-[#35abe7] transition"
                                aria-label="Instagram">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.2 2.4.4.6.2 1 .4 1.4.8.4.4.7.8.8 1.4.2.5.3 1.2.4 2.4.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.9-.4 2.4-.2.6-.4 1-.8 1.4-.4.4-.8.7-1.4.8-.5.2-1.2.3-2.4.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.2-2.4-.4-.6-.2-1-.4-1.4-.8-.4-.4-.7-.8-.8-1.4-.2-.5-.3-1.2-.4-2.4C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-1.9.4-2.4.2-.6.4-1 .8-1.4.4-.4.8-.7 1.4-.8.5-.2 1.2-.3 2.4-.4C8.4 2.2 8.8 2.2 12 2.2m0 1.8c-3.1 0-3.5 0-4.7.1-1 .1-1.5.2-1.9.3-.5.2-.8.3-1.1.6-.3.3-.5.6-.6 1.1-.1.4-.3.9-.3 1.9-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c.1 1 .2 1.5.3 1.9.2.5.3.8.6 1.1.3.3.6.5 1.1.6.4.1.9.3 1.9.3 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1 0 1.5-.2 1.9-.3.5-.2.8-.3 1.1-.6.3-.3.5-.6.6-1.1.1-.4.3-.9.3-1.9.1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c0-1-.2-1.5-.3-1.9-.2-.5-.3-.8-.6-1.1-.3-.3-.6-.5-1.1-.6-.4-.1-.9-.3-1.9-.3-1.2-.1-1.6-.1-4.7-.1m0 2.3a5.7 5.7 0 1 1 0 11.4 5.7 5.7 0 0 1 0-11.4m0 1.8a3.9 3.9 0 1 0 0 7.8 3.9 3.9 0 0 0 0-7.8M17.6 6a1.3 1.3 0 1 0 0 2.6 1.3 1.3 0 0 0 0-2.6z" />
                                </svg>
                            </a>
                        @endif
                        @if ($linkedin)
                            <a href="{{ $linkedin }}" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-[#35abe7] transition"
                                aria-label="LinkedIn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M6.94 6.5a2.19 2.19 0 1 1-4.38 0 2.19 2.19 0 0 1 4.38 0M2.88 8.82h3.82v12.3H2.88zM9.08 8.82h3.66v1.68h.05c.51-.96 1.76-1.98 3.62-1.98 3.88 0 4.6 2.55 4.6 5.86v6.74h-3.82v-5.98c0-1.43-.03-3.28-2-3.28-2 0-2.3 1.56-2.3 3.17v6.09H9.08z" />
                                </svg>
                            </a>
                        @endif
                        @if ($youtube)
                            <a href="{{ $youtube }}" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-[#35abe7] transition"
                                aria-label="YouTube">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6a3 3 0 0 0-2.1 2.1C0 8.1 0 12 0 12s0 3.9.6 5.8a3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1C24 15.9 24 12 24 12s0-3.9-.5-5.8zM9.5 15.5v-7l6 3.5-6 3.5z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <div class="mb-4 text-sm font-semibold tracking-wider text-gray-300 uppercase">Quick Links</div>
                <ul class="space-y-2 text-sm">
                    @forelse ($topMenus as $item)
                        <li>
                            <a href="{{ $item->link }}"
                                class="text-gray-300 transition hover:text-[#35abe7]">{{ $item->title }}</a>
                        </li>
                    @empty
                        <li class="text-gray-400">No links available</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <div class="mb-4 text-sm font-semibold tracking-wider text-gray-300 uppercase">Resources</div>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><a class="transition hover:text-[#35abe7]" href="{{ url('/admissions') }}">Admissions</a></li>
                    <li><a class="transition hover:text-[#35abe7]" href="{{ url('/departments') }}">Departments</a></li>
                    <li><a class="transition hover:text-[#35abe7]" href="{{ url('/events') }}">Events</a></li>
                    <li><a class="transition hover:text-[#35abe7]" href="{{ url('/contact') }}">Contact</a></li>
                </ul>
            </div>

            <div>
                <div class="mb-4 text-sm font-semibold tracking-wider text-gray-300 uppercase">Useful Links</div>
                <ul class="space-y-2 text-sm">
                    @forelse ($footerLinks as $fl)
                        @if (!empty($fl['title']) && !empty($fl['url']))
                            <li>
                                <a href="{{ $fl['url'] }}" class="text-gray-300 transition hover:text-[#35abe7]" target="_blank"
                                    rel="noopener">{{ $fl['title'] }}</a>
                            </li>
                        @endif
                    @empty
                        <li class="text-gray-400">No useful links added</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <div class="mb-4 text-sm font-semibold tracking-wider text-gray-300 uppercase">Locate Us</div>
                <div class="overflow-hidden rounded-lg shadow ring-1 ring-black/5">
                    <iframe
                        src="{{ $mapUrl ?: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28028.902078179204!2d77.199!3d28.6139!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0:0x0!2s' . urlencode($collegeName) . '!5e0!3m2!1sen!2sin!4v' . time() }}"
                        width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" title="Map to {{ $collegeName }}"></iframe>
                </div>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($collegeName) }}" target="_blank"
                    rel="noopener" class="inline-flex items-center gap-2 mt-3 text-sm text-[#35abe7] hover:text-white">
                    <span>Get Directions</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 3l7 7-7 7v-4H3v-6h11V3z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-[#0d3a52]/60 border-t border-white/10">
        <div class="px-4 py-4 mx-auto max-w-7xl">
            <div class="flex flex-col items-center justify-between gap-3 text-sm text-gray-300 md:flex-row">
                <div>© {{ date('Y') }} {{ $collegeName }}. All rights reserved.</div>
                <div class="flex items-center gap-4">
                    <a href="{{ url('/privacy-policy') }}" class="hover:text-white">Privacy Policy</a>
                    <span class="text-white/20">|</span>
                    <a href="{{ url('/terms') }}" class="hover:text-white">Terms</a>
                    <span class="text-white/20">|</span>
                    <a href="{{ url('/contact') }}" class="hover:text-white">Contact</a>
                </div>
            </div>
        </div>
    </div>

    <button type="button" aria-label="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'})"
        class="fixed z-40 hidden p-3 text-white rounded-full shadow-lg bottom-6 right-6 bg-[#35abe7] hover:bg-[#1f90cc]"
        id="backToTop">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.59 5.58L20 12l-8-8-8 8z" />
        </svg>
    </button>
    <script>
        (function () {
            var btn = document.getElementById('backToTop');
            if (!btn) return;
            window.addEventListener('scroll', function () {
                if (window.scrollY > 300) { btn.classList.remove('hidden'); } else { btn.classList.add('hidden'); }
            });
        })();
    </script>
</footer>
