@props(['block'])

@php
    $type = $block['type'] ?? '';
    $content = $block['content'] ?? '';

    // Common style attributes for Quill/Text blocks (From your original code)
    $style = collect([
        'font-size'   => ($block['fontSize'] ?? null) ? $block['fontSize'] . 'px' : null,
        'color'       => $block['color'] ?? null,
        'text-align'  => $block['textAlign'] ?? null,
        'line-height' => $block['lineHeight'] ?? null,
        'font-weight' => ($block['bold'] ?? false) ? 'bold' : null,
        'font-style'  => ($block['italic'] ?? false) ? 'italic' : null,
        'text-decoration' => ($block['underline'] ?? false) ? 'underline' : null,
        'margin-bottom' => '1rem',
    ])->filter()->map(fn($v, $k) => "{$k}: {$v}")->implode('; ');
@endphp

@switch($type)
    {{-- ==================== SECTION (Original) ==================== --}}
    @case('section')
        <section
            x-data="{ open: {{ $block['expanded'] ?? false ? 'true' : 'false' }} }"
            class="p-4 mb-6 border border-gray-200 shadow-sm rounded-2xl bg-gray-50"
        >
            <div
                @click="open = !open"
                class="flex items-center justify-between cursor-pointer select-none"
            >
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ $block['title'] ?? 'Untitled Section' }}
                </h2>

                {{-- Toggle Icons (Original) --}}
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 5v14m7-7H5" />
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <div x-show="open" x-collapse class="mt-4 space-y-6">
                @if (!empty($block['blocks']) && is_array($block['blocks']))
                    @foreach ($block['blocks'] as $sub)
                        <x-page-block :block="$sub" />
                    @endforeach
                @else
                    <p class="italic text-gray-400">No content in this section.</p>
                @endif
            </div>
        </section>
        @break

    {{-- ==================== HEADING, TEXT (Original) ==================== --}}
    @case('heading')
    @case('text')
        <div
            class="mb-4 prose-sm prose prose-gray max-w-none"
            style="{{ $style }}"
        >
            {!! $content !!}
        </div>
        @break

    {{-- ==================== IMAGE (Original) ==================== --}}
    @case('image')
        @if (!empty($block['src']))
            <div class="my-6 text-center">
                <img
                    src="{{ $block['src'] }}"
                    alt="{{ $block['alt'] ?? 'Image' }}"
                    style="max-width: 100%; height: auto; {{ $style }}"
                    class="object-contain mx-auto rounded-lg shadow-md"
                    loading="lazy"
                />
                @if (!empty($block['caption']))
                    <p class="mt-2 text-sm text-gray-500">{{ $block['caption'] }}</p>
                @endif
            </div>
        @endif
        @break

    {{-- ==================== VIDEO (Original) ==================== --}}
    @case('video')
        @if (!empty($block['src']))
            <div class="my-6 text-center">
                <video
                    src="{{ $block['src'] }}"
                    controls
                    class="max-w-full mx-auto rounded-lg shadow-md"
                    style="height: {{ $block['height'] ?? '315px' }}; max-width: 100%;"
                ></video>
            </div>
        @endif
        @break

    {{-- ==================== INTRO (Homepage) ==================== --}}
    @case('intro')
        @php
            $layout = $block['layout'] ?? 'left';
            $img = $block['image'] ?? '';
            $heading = $block['heading'] ?? '';
            $text = $block['text'] ?? '';
            $btnText = $block['buttonText'] ?? '';
            $btnHref = $block['buttonHref'] ?? '';
        @endphp
        <section class="py-10">
            <div class="container grid items-center grid-cols-1 gap-8 px-4 mx-auto md:grid-cols-2">
                @if ($layout === 'right' || $layout === 'top')
                    <div class="{{ $layout === 'top' ? 'order-1 md:col-span-2' : 'order-2' }}">
                        @if ($img)
                            <img src="{{ $img }}" class="w-full rounded-xl shadow-md" alt="">
                        @endif
                    </div>
                @endif

                <div class="order-1">
                    @if ($heading)
                        <h2 class="text-2xl font-bold text-gray-900 md:text-3xl">{{ $heading }}</h2>
                    @endif
                    @if ($text)
                        <p class="mt-3 text-gray-600">{{ $text }}</p>
                    @endif
                    @if ($btnText && $btnHref)
                        <a href="{{ $btnHref }}" class="inline-block px-5 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700">{{ $btnText }}</a>
                    @endif
                </div>

                @if ($layout === 'left')
                    <div class="order-2">
                        @if ($img)
                            <img src="{{ $img }}" class="w-full rounded-xl shadow-md" alt="">
                        @endif
                    </div>
                @endif
            </div>
        </section>
        @break

    {{-- ==================== LINKS GRID (Homepage) ==================== --}}
    @case('linkGrid')
        @php
            $title = $block['title'] ?? '';
            $columns = max(2, min(4, (int)($block['columns'] ?? 3)));
            $items = $block['items'] ?? [];
            $gridClass = match($columns) { 2 => 'sm:grid-cols-2', 4 => 'sm:grid-cols-2 lg:grid-cols-4', default => 'sm:grid-cols-2 lg:grid-cols-3' };
        @endphp
        <section class="py-8">
            <div class="container px-4 mx-auto">
                @if ($title)
                    <h3 class="mb-4 text-xl font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                <div class="grid grid-cols-1 gap-4 {{ $gridClass }}">
                    @foreach ($items as $it)
                        @php $t = $it['title'] ?? ''; $href = $it['href'] ?? '#'; $isNew = !empty($it['isNew']); @endphp
                        <a href="{{ $href }}" class="flex items-center justify-between p-4 transition bg-white border rounded-lg shadow-sm hover:shadow">
                            <span class="text-sm font-medium text-gray-800">{{ $t }}</span>
                            <span class="flex items-center gap-2">
                                @if ($isNew)
                                    <span class="px-2 py-0.5 text-xs font-semibold text-white bg-green-600 rounded-full">NEW</span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @break

    {{-- ==================== PDF (Original & Working) ==================== --}}
    @case('pdf')
        @if (!empty($block['src']))
            <div class="my-6">
                <iframe
                    src="{{ $block['src'] }}"
                    class="w-full h-[700px] border rounded-lg shadow-inner"
                    frameborder="0"
                    loading="lazy"
                    no-referrer="true" download="false"
                ></iframe>
            </div>
        @endif
        @break


    {{-- ==================== NEW: EMBED (YouTube, Vimeo, etc.) ==================== --}}
    @case('embed')
        @php
            $embedUrl = null;
            $src = $block['src'] ?? '';
            if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $src, $matches)) {
                $embedUrl = "https://www.youtube.com/embed/" . $matches[2] . '?rel=0';
            }
        @endphp
        @if ($embedUrl)
            <div class="my-6 aspect-w-16 aspect-h-9">
                <iframe
                    src="{{ $embedUrl }}"
                    class="w-full h-[500px] rounded-lg shadow-md"
                    style="height: {{ $block['height'] ?? '315px' }}; max-width: 100%;"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
        @elseif (!empty($src))
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                Unsupported embed URL: {{ $src }}
            </div>
        @endif
        @break

    {{-- ==================== NEW: DIVIDER ==================== --}}
    @case('divider')
        <hr class="my-8 border-gray-200" />
        @break

    {{-- ==================== NEW: BUTTON ==================== --}}
    @case('button')
        @php
            $buttonHref = $block['href'] ?? '#';
            $buttonTarget = ($block['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
            $buttonText = $block['text'] ?? ($block['content'] ?? 'Click Here');
            $buttonAlignment = $block['align'] ?? 'left';
            $buttonAlignClass = match($buttonAlignment) {
                'center' => 'text-center',
                'right' => 'text-right',
                default => 'text-left',
            };
        @endphp
        <div class="my-6 {{ $buttonAlignClass }}">
            <a
                href="{{ $buttonHref }}"
                target="{{ $buttonTarget }}"
                rel="{{ $buttonTarget === '_blank' ? 'noopener noreferrer' : '' }}"
                class="inline-block px-5 py-2 font-medium text-white transition-colors duration-200 bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                {{ $buttonText }}
            </a>
        </div>
        @break

    {{-- ==================== NEW: CODE BLOCK ==================== --}}
    @case('code')
        <div class="my-6">
            {{-- $content is already defined in the main @php block --}}
            <pre class="p-4 overflow-x-auto text-sm text-white bg-gray-800 rounded-lg shadow-md"><code>{{ $content }}</code></pre>
        </div>
        @break

    {{-- ==================== NEW: ANNOUNCEMENTS ==================== --}}
    @case('announcements')
        @php
            $title = $block['section_title'] ?? 'Announcements';
            $contentType = $block['content_type'] ?? 'student';
            $limit = max(1, (int)($block['display_count'] ?? 5));
            $items = \App\Models\Announcement::where('type', $contentType)->where('status', true)->latest()->take($limit)->get();
        @endphp
        <section class="py-6">
            <h3 class="mb-3 text-xl font-semibold text-gray-900">{{ $title }}</h3>
            <ul class="space-y-2">
                @forelse($items as $it)
                    <li class="p-3 bg-white border rounded shadow-sm">
                        <h4 class="font-medium text-gray-800">{{ $it->title }}</h4>
                        <div class="mt-1 text-sm text-gray-600">{!! \Illuminate\Support\Str::limit(strip_tags($it->content), 160) !!}</div>
                    </li>
                @empty
                    <li class="text-gray-500">No announcements.</li>
                @endforelse
            </ul>
        </section>
        @break

    {{-- ==================== NEW: EVENTS (basic grouped list) ==================== --}}
    @case('events')
        @php
            $title = $block['section_title'] ?? "What's Happening";
            $desc = $block['section_description'] ?? '';
            $categories = \App\Models\EventCategory::with(['items' => function($q){ $q->orderBy('event_date', 'desc')->take(6); }])->get();
        @endphp
        <section class="py-6">
            <div class="mb-4">
                <h3 class="text-xl font-semibold text-gray-900">{{ $title }}</h3>
                @if($desc)
                    <p class="mt-1 text-gray-600">{{ $desc }}</p>
                @endif
            </div>
            <div class="space-y-6">
                @forelse($categories as $cat)
                    <div>
                        <h4 class="mb-2 text-lg font-medium text-gray-800">{{ $cat->name }}</h4>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @forelse($cat->items as $ev)
                                <div class="p-3 bg-white border rounded shadow-sm">
                                    <div class="text-sm text-gray-500">{{ optional($ev->event_date)->format('M d, Y h:i A') }}</div>
                                    <div class="font-medium text-gray-800">{{ $ev->title }}</div>
                                    @if($ev->venue)
                                        <div class="text-sm text-gray-600">Venue: {{ $ev->venue }}</div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-gray-500">No events.</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No event categories found.</p>
                @endforelse
            </div>
        </section>
        @break

    {{-- ==================== NEW: ACADEMIC CALENDAR ==================== --}}
    @case('academic_calendar')
        @php
            $title = $block['section_title'] ?? 'Academic Calendar';
            $limit = max(1, (int)($block['item_count'] ?? 7));
            $upcoming = \App\Models\AcademicCalendar::where('status', true)->orderBy('event_datetime')->take($limit)->get();
        @endphp
        <section class="py-6">
            <h3 class="mb-3 text-xl font-semibold text-gray-900">{{ $title }}</h3>
            <ul class="space-y-2">
                @forelse($upcoming as $ev)
                    <li class="flex items-start gap-3 p-3 bg-white border rounded shadow-sm">
                        <span class="px-2 py-1 text-xs text-white bg-blue-600 rounded">{{ optional($ev->event_datetime)->format('d M, Y') }}</span>
                        <div>
                            <div class="font-medium text-gray-800">{{ $ev->title }}</div>
                            @if($ev->link_href)
                                <a href="{{ $ev->link_href }}" target="_blank" rel="noopener" class="text-sm text-blue-600 underline">More</a>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="text-gray-500">No upcoming items.</li>
                @endforelse
            </ul>
        </section>
        @break

    {{-- ==================== NEW: IMAGE + TEXT ==================== --}}
    @case('image_text')
        @php
            $layout = $block['layout'] ?? 'image_left';
            $img = $block['image'] ?? '';
            $html = $block['contentHtml'] ?? ($block['content'] ?? '');
        @endphp
        <section class="py-8">
            <div class="grid items-center grid-cols-1 gap-6 md:grid-cols-2">
                @if($layout === 'image_left')
                    <div>@if($img)<img src="{{ $img }}" class="w-full rounded-xl shadow" alt="">@endif</div>
                    <div class="prose max-w-none">{!! $html !!}</div>
                @else
                    <div class="prose max-w-none">{!! $html !!}</div>
                    <div>@if($img)<img src="{{ $img }}" class="w-full rounded-xl shadow" alt="">@endif</div>
                @endif
            </div>
        </section>
        @break

    {{-- ==================== NEW: GALLERY (simple grid of latest by category) ==================== --}}
    @case('gallery')
        @php
            $title = $block['section_title'] ?? 'Gallery';
            $cats = \App\Models\GalleryCategory::with(['images' => function($q){ $q->latest()->take(12); }])->get();
            $images = $cats->flatMap->images->take(12);
        @endphp
        <section class="py-6">
            <h3 class="mb-3 text-xl font-semibold text-gray-900">{{ $title }}</h3>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                @forelse($images as $img)
                    <div class="overflow-hidden bg-white border rounded shadow-sm">
                        <img src="{{ asset('storage/'.$img->image) }}" alt="{{ $img->title }}" class="object-cover w-full h-40">
                    </div>
                @empty
                    <p class="text-gray-500">No images.</p>
                @endforelse
            </div>
        </section>
        @break

    {{-- ==================== NEW: TESTIMONIALS (simple list) ==================== --}}
    @case('testimonials')
        @php
            $title = $block['section_title'] ?? 'Testimonials';
            $testimonials = \App\Models\Testimonial::where('status', true)->latest()->take(8)->get();
        @endphp
        <section class="py-6">
            <h3 class="mb-3 text-xl font-semibold text-gray-900">{{ $title }}</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($testimonials as $t)
                    <div class="p-4 bg-white border rounded shadow-sm">
                        <div class="flex items-center gap-3">
                            @if($t->student_image)
                                <img src="{{ asset('storage/'.$t->student_image) }}" class="object-cover w-10 h-10 rounded-full" alt="{{ $t->student_name }}">
                            @endif
                            <div class="font-medium text-gray-800">{{ $t->student_name }}</div>
                        </div>
                        <p class="mt-2 text-gray-600">{{ $t->testimonial_text }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No testimonials.</p>
                @endforelse
            </div>
        </section>
        @break

    {{-- ==================== NEW: WHY CHOOSE US (grid) ==================== --}}
    @case('why_choose_us')
        @php
            $title = $block['section_title'] ?? 'Why Choose Us';
            $desc = $block['section_description'] ?? '';
            $items = \App\Models\WhyChooseUs::orderBy('sort_order')->get();
        @endphp
        <section class="py-6">
            <div class="mb-4">
                <h3 class="text-xl font-semibold text-gray-900">{{ $title }}</h3>
                @if($desc)
                    <p class="mt-1 text-gray-600">{{ $desc }}</p>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                @forelse($items as $it)
                    <div class="p-4 text-center bg-white border rounded shadow-sm">
                        @if($it->icon_or_image)
                            <img src="{{ asset('storage/'.$it->icon_or_image) }}" class="w-12 h-12 mx-auto mb-2" alt="{{ $it->title }}">
                        @endif
                        <div class="font-medium text-gray-800">{{ $it->title }}</div>
                        <div class="mt-1 text-sm text-gray-600">{{ $it->description }}</div>
                    </div>
                @empty
                    <p class="text-gray-500">No items.</p>
                @endforelse
            </div>
        </section>
        @break

    {{-- ==================== DEFAULT (Original) ==================== --}}
    @default
        <div class="p-4 text-sm text-gray-500 bg-gray-100 rounded-lg">
            Unknown block type: <strong>{{ $type }}</strong>
        </div>
@endswitch

