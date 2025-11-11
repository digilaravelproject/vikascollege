@props(['title', 'items'])

{{-- === CSS FOR PIXEL PERFECT SCROLLBAR === --}}
{{-- CSS ko issi file mein add kar raha hoon, jaisa aapne kaha --}}
<style>
    .custom-scrollbar-{{ $attributes->get('data-unique-id', 'default') }}::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scrollbar-{{ $attributes->get('data-unique-id', 'default') }}::-webkit-scrollbar-track {
        background: #f1f1f1;
        /* Scrollbar track color */
    }

    .custom-scrollbar-{{ $attributes->get('data-unique-id', 'default') }}::-webkit-scrollbar-thumb {
        background: #1f497d;
        /* Scrollbar handle color (header jaisa) */
    }
</style>
{{-- === END OF CSS === --}}


{{--
Yeh hai POORA box.
Aapka <h2> (jo pehle file mein tha) hata diya gaya hai,
    kyunki $title ab blue bar mein aa raha hai.
    --}}
    <div class="w-full bg-white shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="100">

        {{--
        NOTES:
        - Background Color: #1f497d (Image se)
        - Font: Bold, Uppercase, Sans-Serif
        --}}
        <div class="bg-[#1f497d] text-white text-center font-bold uppercase py-3 px-5">
            <h3 class="text-xl tracking-wide font-sans">
                {{ $title }} {{-- Yahaan "STUDENT CORNER" ya "FACULTY CORNER" ayega --}}
            </h3>
        </div>

        {{--
        NOTES:
        - Height: h-80 (320px) fixed di hai. Aap adjust kar sakte hain.
        - Scrollbar: 'custom-scrollbar-...' class add ki hai.
        --}}
        <div class="p-6 h-80 overflow-y-auto custom-scrollbar-{{ $attributes->get('data-unique-id', 'default') }}">

            @if ($items->isEmpty())
                <p class="text-gray-500 font-sans">
                    No announcements found.
                </p>
            @else
                {{--
                IMAGE-MATCHING CONTENT (Paragraphs)
                Image mein simple paragraphs hain. Aapka purana code links (<a>) use kar raha tha.
                    Pixel-perfect ke liye, main paragraphs (p) use kar raha hoon.
                    --}}
                    <div class="space-y-4 text-gray-700 font-sans text-sm">

                        @foreach ($items as $item)
                            {{--
                            IMPORTANT: Image mein paragraphs hain.
                            Aapko yahaan $item->description ya $item->content use karna hoga.
                            Aapke purane code mein $item->title tha, main wohi use kar raha hoon,
                            lekin <p> tag ke andar.
                                --}}
                            <p>
                                {{ $item->title }}
                                {{-- Agar description hai toh $item->description use karein --}}
                            </p>
                        @endforeach

                    </div>
            @endif

                {{--
                NOTES:
                - Text Color: #c00000 (Image se)
                --}}
                <a href="#" class="inline-block mt-5 font-semibold text-[#c00000] hover:underline font-sans">
                    Read More....
                </a>
        </div>
    </div>
