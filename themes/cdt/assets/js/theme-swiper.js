// Lazy-loaded Swiper chunk. Only fetched when a slider exists on the page
// (posts featured slider, testimonials, product testimonials). Its CSS stays
// in the eager bundle so slides are laid out correctly before JS arrives.
import { Swiper } from 'swiper';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';

// Register the only modules this theme uses so inline `new Swiper(...)` calls
// in Blade views (e.g. posts/index featured-slider) work without a modules array.
Swiper.use([Autoplay, Navigation, Pagination]);

// Expose globally for inline scripts in partials that poll for `Swiper`.
window.Swiper = Swiper;

export { Swiper };
