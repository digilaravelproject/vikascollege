@if(setting('banner_image'))
    <section class=" banner-image relative w-full overflow-hidden">
        <!-- Banner Image -->
        <img src="{{ asset('storage/' . setting('banner_image')) }}" alt="Banner"
            class="w-full h-[380px] sm:h-[450px] lg:h-[520px] xl:h-[600px] object-cover object-center transition-transform duration-700 hover:scale-105">

        <!-- Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/30 flex flex-col items-center justify-center text-white text-center px-5 sm:px-16">

            <!-- Banner Title -->
            @if(setting('banner_heading'))
                <h1
                    class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-wide drop-shadow-lg leading-tight">
                    {{ setting('banner_heading') }}
                </h1>
            @endif

            <!-- Optional Subtext -->
            @if(setting('banner_subheading'))
                <p class="mt-4 text-base sm:text-lg md:text-xl text-gray-200 max-w-2xl">
                    {{ setting('banner_subheading') }}
                </p>
            @endif

            <!-- CTA Button -->
            @if(setting('banner_button_text') && setting('banner_button_link'))
                <a href="{{ setting('banner_button_link') }}"
                    class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm sm:text-base px-6 sm:px-8 py-3 rounded-full shadow-lg transition duration-300 transform hover:scale-105">
                    {{ setting('banner_button_text') }}
                </a>
            @endif
        </div>
    </section>
@endif
<!-- 🔹 Marquee Section -->
<section class="w-full bg-[#013954] text-white overflow-hidden">
    <div
        class="flex whitespace-nowrap py-3 text-sm sm:text-base font-medium tracking-wide animate-[marquee_25s_linear_infinite]">
        <span class="mx-8 inline-block">
            🚀 Welcome to our website! Enjoy amazing deals and latest updates every week!
        </span>
        <span class="mx-8 inline-block">
            🎉 Don’t miss our upcoming offers — stay tuned!
        </span>
        <span class="mx-8 inline-block">
            🌟 Quality products, trusted service, and unbeatable prices!
        </span>
    </div>
</section>

<style>
    .banner-image {

        font-family: 'Montserrat', sans-serif;
    }

    @keyframes marquee {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }
</style>