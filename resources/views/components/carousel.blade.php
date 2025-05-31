@props(['items' => [], 'id' => 'carousel', 'autoplay' => true, 'interval' => 5000])

<div class="relative w-full" x-data="carousel('{{ $id }}', {{ $autoplay ? 'true' : 'false' }}, {{ $interval }})">
    <!-- Carousel wrapper -->
    <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
        @foreach($items as $index => $item)
            <div x-show="currentSlide === {{ $index }}" 
                 x-transition:enter="transition ease-in-out duration-700 transform"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in-out duration-700 transform"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-full"
                 class="absolute inset-0 w-full h-full">
                {!! $item !!}
            </div>
        @endforeach
    </div>

    @if(count($items) > 1)
        <!-- Slider indicators -->
        <div class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-5 left-1/2">
            @foreach($items as $index => $item)
                <button type="button" 
                        class="w-3 h-3 rounded-full transition-all duration-300"
                        :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                        @click="goToSlide({{ $index }})"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>

        <!-- Slider controls -->
        <button type="button" 
                class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" 
                @click="previousSlide()">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                <svg class="w-4 h-4 text-white dark:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                </svg>
                <span class="sr-only">Previous</span>
            </span>
        </button>
        <button type="button" 
                class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" 
                @click="nextSlide()">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                <svg class="w-4 h-4 text-white dark:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                </svg>
                <span class="sr-only">Next</span>
            </span>
        </button>
    @endif
</div>

<script>
function carousel(id, autoplay = true, interval = 5000) {
    return {
        currentSlide: 0,
        totalSlides: {{ count($items) }},
        autoplayInterval: null,
        
        init() {
            if (autoplay && this.totalSlides > 1) {
                this.startAutoplay();
            }
        },
        
        goToSlide(index) {
            this.currentSlide = index;
            this.resetAutoplay();
        },
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
            this.resetAutoplay();
        },
        
        previousSlide() {
            this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
            this.resetAutoplay();
        },
        
        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, interval);
        },
        
        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
                this.autoplayInterval = null;
            }
        },
        
        resetAutoplay() {
            if (autoplay && this.totalSlides > 1) {
                this.stopAutoplay();
                this.startAutoplay();
            }
        }
    }
}
</script>
