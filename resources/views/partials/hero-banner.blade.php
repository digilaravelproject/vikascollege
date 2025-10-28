<!-- 🔹 Marquee Section -->
<section class="w-full bg-[#013954] text-white overflow-hidden">
    <div
        class="flex whitespace-nowrap py-3 text-sm sm:text-base font-medium tracking-wide animate-[marquee_25s_linear_infinite]">
        <span class="inline-block mx-8">
            🚀 Welcome to our website! Enjoy amazing deals and latest updates every week!
        </span>
        <span class="inline-block mx-8">
            🎉 Don’t miss our upcoming offers — stay tuned!
        </span>
        <span class="inline-block mx-8">
            🌟 Quality products, trusted service, and unbeatable prices!
        </span>
    </div>
</section>
@if(setting('banner_image'))
    <section class="relative w-full overflow-hidden  banner-image">
        <!-- Banner Image -->
        <img src="{{ asset('storage/' . setting('banner_image')) }}" alt="Banner"
            class="w-full h-[380px] sm:h-[450px] lg:h-[520px] xl:h-[600px] object-cover object-center transition-transform duration-700 hover:scale-105">

        <!-- Overlay -->
        <div
            class="absolute inset-0 flex flex-col items-center justify-center px-5 text-center text-white bg-gradient-to-b from-black/50 via-black/40 to-black/30 sm:px-16">

            <!-- Banner Title -->
            @if(setting('banner_heading'))
                <h1
                    class="text-2xl font-extrabold leading-tight tracking-wide sm:text-3xl md:text-4xl lg:text-5xl drop-shadow-lg">
                    {{ setting('banner_heading') }}
                </h1>
            @endif

            <!-- Optional Subtext -->
            @if(setting('banner_subheading'))
                <p class="max-w-2xl mt-4 text-base text-gray-200 sm:text-lg md:text-xl">
                    {{ setting('banner_subheading') }}
                </p>
            @endif

            <!-- CTA Button -->
            @if(setting('banner_button_text') && setting('banner_button_link'))
                <a href="{{ setting('banner_button_link') }}"
                    class="inline-block px-6 py-3 mt-6 text-sm font-semibold text-white transition duration-300 transform bg-blue-600 rounded-full shadow-lg hover:bg-blue-700 sm:text-base sm:px-8 hover:scale-105">
                    {{ setting('banner_button_text') }}
                </a>
            @endif
        </div>
    </section>
@endif


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