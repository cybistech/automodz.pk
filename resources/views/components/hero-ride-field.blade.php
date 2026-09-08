@php
    $heroSlides = \App\Support\Seo::heroSlides(shuffle: true);
@endphp

<div class="hero-rides" id="hero-rides" aria-hidden="true">
    <div class="hero-rides__slideshow">
        @foreach($heroSlides as $index => $slide)
            <div
                class="hero-rides__slide {{ $index === 0 ? 'is-active' : '' }}"
                style="background-image: url('{{ $slide['src'] }}')"
                role="img"
                aria-label="{{ $slide['alt'] }}"
            ></div>
        @endforeach
    </div>

    <div class="hero-rides__overlay"></div>
    <div class="hero-rides__vignette"></div>
</div>

<script>
(function () {
    const root = document.getElementById('hero-rides');
    const slides = Array.from(root?.querySelectorAll('.hero-rides__slide') ?? []);
    if (!root || slides.length < 2) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) return;

    let slideIndex = 0;
    let slideTimer = null;

    function rotateSlides() {
        slides[slideIndex]?.classList.remove('is-active');
        slideIndex = (slideIndex + 1) % slides.length;
        slides[slideIndex]?.classList.add('is-active');
    }

    slideTimer = window.setInterval(rotateSlides, 7000);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (slideTimer) window.clearInterval(slideTimer);
            slideTimer = null;
        } else if (!slideTimer) {
            slideTimer = window.setInterval(rotateSlides, 7000);
        }
    });
})();
</script>
