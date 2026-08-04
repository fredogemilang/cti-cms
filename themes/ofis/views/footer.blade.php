<!-- About BPT Pre-Footer Section -->
<section id="about" class="relative w-full bg-ofis-teal">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-5 gap-8 items-center" style="padding-top:100px;padding-bottom:100px;">
    <div class="relative flex flex-col gap-5 md:col-span-1">
      <img src="{{ theme_asset('aboutbptcompany.png-COZKYyc6.webp') }}" alt="About BPT Company" class="max-w-full h-auto rounded-2xl" loading="lazy"/>
    </div>
    <div class="relative flex flex-col gap-5 md:col-span-4">
      <h2 class="text-4xl font-bold text-white leading-tight">About BPT</h2>
      <div class="prose max-w-none text-base text-white">
        <p class="text-white text-base font-normal leading-6">
          {{ t('footer.about_p1', 'As a PT Computrade Technology International (CTI Group) subsidiary, Blue Power Technology (BPT) embarked on its journey in 2011 and offers transformative solutions designed to propel customer business into the future. We aim to help customers succeed in the digital era by streamlining and improving their systems with agility and cost-effectiveness.') }}
        </p>
        <p class="text-white text-base font-normal leading-6 mt-4">
          {{ t('footer.about_p2', 'Powered by strategic collaborations with leading IT industry players and business partners, BPT delivers state-of-the-art digital solutions tailored precisely to customers\' unique requirements. Our focus extends beyond mere provision to a comprehensive partnership, ensuring our customer goals are met in a rapidly evolving digital landscape.') }}
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-[#f0f0f0] text-ofis-ink relative overflow-hidden">
  <div class="footer-map pointer-events-none absolute inset-0" aria-hidden="true"></div>

  <div class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <a href="{{ url('/') }}" class="inline-block">
      <img src="{{ theme_asset('Logo-OFIS-e1711423097777.png-M5Jmiuvo.webp') }}" alt="Logo OFIS" class="h-12 w-auto"/>
    </a>

    <hr class="border-t border-black/10 my-8"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
      <!-- Connect with us -->
      <div class="flex flex-col gap-5">
        <h6 class="text-base font-bold text-ofis-ink tracking-[0.15em] uppercase">{{ t('footer.connect_title', 'Connect With Us') }}</h6>
        <ul class="flex flex-col gap-5">
          <li>
            <a href="https://wa.me/6282299922278" target="_blank" rel="noopener" class="inline-flex items-center gap-4 text-ofis-ink hover:text-ofis-teal transition">
              <span class="footer-icon">
                <svg viewBox="0 0 448 512" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
              </span>
              <span>+62 822-9992-2278</span>
            </a>
          </li>
          <li>
            <a href="mailto:marketing@bluepowertechnology.com" class="inline-flex items-center gap-4 text-ofis-ink hover:text-ofis-teal transition">
              <span class="footer-icon">
                <svg viewBox="0 0 512 512" fill="currentColor"><path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"/></svg>
              </span>
              <span>marketing@bluepowertechnology.com</span>
            </a>
          </li>
          <li>
            <a href="https://www.bluepowertechnology.com/" target="_blank" rel="noopener" class="inline-flex items-center gap-4 text-ofis-ink hover:text-ofis-teal transition">
              <span class="footer-icon">
                <svg viewBox="0 0 496 512" fill="currentColor"><path d="M336.5 160C322 70.7 287.8 8 248 8s-74 62.7-88.5 152h177zM152 256c0 22.2 1.2 43.5 3.3 64h185.3c2.1-20.5 3.3-41.8 3.3-64s-1.2-43.5-3.3-64H155.3c-2.1 20.5-3.3 41.8-3.3 64zm324.7-96c-28.6-67.9-86.5-120.4-158-141.6 24.4 33.8 41.2 84.7 50 141.6h108zM177.2 18.4C105.8 39.6 47.8 92.1 19.3 160h108c8.7-56.9 25.5-107.8 49.9-141.6zM487.4 192H372.7c2.1 21 3.3 42.5 3.3 64s-1.2 43-3.3 64h114.6c5.5-20.5 8.6-41.8 8.6-64s-3.1-43.5-8.5-64zM120 256c0-21.5 1.2-43 3.3-64H8.6C3.2 212.5 0 233.8 0 256s3.2 43.5 8.6 64h114.6c-2-21-3.2-42.5-3.2-64zm39.5 96c14.5 89.3 48.7 152 88.5 152s74-62.7 88.5-152h-177zm159.3 141.6c71.4-21.2 129.4-73.7 158-141.6h-108c-8.8 56.9-25.6 107.8-50 141.6zM19.3 352c28.6 67.9 86.5 120.4 158 141.6-24.4-33.8-41.2-84.7-50-141.6h-108z"/></svg>
              </span>
              <span>www.bluepowertechnology.com</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Our office + Join us -->
      <div class="flex flex-col gap-5">
        <h6 class="text-base font-bold text-ofis-ink tracking-[0.15em] uppercase">{{ t('footer.our_office', 'OUR OFFICE') }}</h6>
        <address class="not-italic text-ofis-ink leading-7">
          Centennial Tower 12th Floor,<br/>
          Jl. Jend. Gatot Subroto Kav. 24 – 25<br/>
          Jakarta, 12930, Indonesia
        </address>

        <h6 class="text-base font-bold text-ofis-ink tracking-[0.15em] uppercase mt-4">{{ t('footer.join_us', 'Join Us') }}</h6>
        <ul class="flex items-center gap-5">
          <li><a href="https://www.facebook.com/bluepowerid/" target="_blank" rel="noopener" aria-label="Facebook" class="text-ofis-ink hover:text-ofis-teal transition inline-block"><svg class="w-5 h-5" viewBox="0 0 320 512" fill="currentColor"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg></a></li>
          <li><a href="https://www.instagram.com/bluepowerid/" target="_blank" rel="noopener" aria-label="Instagram" class="text-ofis-ink hover:text-ofis-teal transition inline-block"><svg class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg></a></li>
          <li><a href="https://id.linkedin.com/company/blue-power-technology" target="_blank" rel="noopener" aria-label="LinkedIn" class="text-ofis-ink hover:text-ofis-teal transition inline-block"><svg class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg></a></li>
          <li><a href="https://www.youtube.com/channel/UCF1I-9jR-FxXz7PPeatYa1w" target="_blank" rel="noopener" aria-label="YouTube" class="text-ofis-ink hover:text-ofis-teal transition inline-block"><svg class="w-5 h-5" viewBox="0 0 576 512" fill="currentColor"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"/></svg></a></li>
          <li><a href="https://www.tiktok.com/@bluepowerid" target="_blank" rel="noopener" aria-label="TikTok" class="text-ofis-ink hover:text-ofis-teal transition inline-block"><svg class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor"><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/></svg></a></li>
        </ul>
      </div>
    </div>

    <hr class="border-t border-black/10 my-8"/>

    <p class="text-sm text-ofis-ink/80">© {{ date('Y') }} Ofis. All rights reserved.</p>
  </div>
</footer>

<style>
  .footer-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 999px;
    border: 1.5px solid currentColor;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .footer-icon > svg { width: 0.9rem; height: 0.9rem; }

  .footer-map {
    background-image: url('{{ theme_asset('footerbg.jpg-DsK3t4Bu.webp') }}');
    background-repeat: no-repeat;
    background-position: right bottom;
    background-size: contain;
  }
</style>
