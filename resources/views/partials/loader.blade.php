<style>
    /* Full Screen Overlay */
    #global-loader {
        position: fixed;
        z-index: 9999999; /* Topmost layer */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
    }

    /* Hidden State */
    .loader-hidden {
        opacity: 0;
        visibility: hidden;
    }

    /* Container for ring and logo */
    .loader-container {
        position: relative;
        width: 100px;  /* Size of loader */
        height: 100px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Rotating Ring */
    .loader-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 3px solid rgba(1, 57, 84, 0.1); /* Light base ring */
        border-top: 3px solid #013954; /* Main Brand Color Ring */
        animation: spin 1s linear infinite;
    }

    /* Logo inside */
    .loader-logo {
        width: 65%; /* Logo size relative to ring */
        height: 65%;
        object-fit: contain;
        border-radius: 50%;
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(0.95); opacity: 0.8; }
    }

    /* Lock Scroll initially */
    body.loading-active {
        overflow: hidden !important;
    }
</style>

<div id="global-loader">
    <div class="loader-container">
        <div class="loader-ring"></div>
        {{-- College Logo Fetch --}}
        <img src="{{ asset('storage/' . setting('college_logo')) }}" alt="Loading..." class="loader-logo">
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.classList.add('loading-active');
    });

    window.addEventListener("load", function () {
        const loader = document.getElementById("global-loader");
        const body = document.body;

        // Thoda delay taaki smooth lage
        setTimeout(function() {
            loader.classList.add("loader-hidden");
            body.classList.remove("loading-active");
            
            // Remove from DOM after fade out
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }, 600); 
    });
</script>