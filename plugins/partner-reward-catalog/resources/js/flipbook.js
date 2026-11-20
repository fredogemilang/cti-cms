import * as pdfjsLib from 'pdfjs-dist';
import pdfjsWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';
import { PageFlip } from 'page-flip';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorkerUrl;

function initPartnerRewardFlipbook() {
  const root = document.getElementById('partner-reward-catalog-root');
  if (!root || root.dataset.flipInitialized === '1') return;
  root.dataset.flipInitialized = '1';

  const pdfUrl = root.dataset.pdfUrl;
  if (!pdfUrl) return;

  const bookEl = document.getElementById('flipbook-stage-book');
  const stageWrapper = document.getElementById('flipbook-viewport');
  const fullscreenTarget = document.getElementById('flipbook-shell');
  const loaderEl = document.getElementById('flipbook-loader');
  const loaderBar = document.getElementById('flipbook-loader-bar');
  const loaderText = document.getElementById('flipbook-loader-text');
  const errorEl = document.getElementById('flipbook-error');

  const btnFirst = document.getElementById('flip-btn-first');
  const btnPrev = document.getElementById('flip-btn-prev');
  const btnNext = document.getElementById('flip-btn-next');
  const btnLast = document.getElementById('flip-btn-last');
  const pageInput = document.getElementById('flip-page-input');
  const pageTotalEl = document.getElementById('flip-page-total');
  const zoomInBtn = document.getElementById('flip-zoom-in');
  const zoomOutBtn = document.getElementById('flip-zoom-out');
  const zoomLabel = document.getElementById('flip-zoom-label');
  const thumbToggleBtn = document.getElementById('flip-thumbs-toggle');
  const thumbDrawer = document.getElementById('flip-thumbs-drawer');
  const fullscreenBtn = document.getElementById('flip-fullscreen');
  const sidePrevBtn = document.getElementById('flip-side-prev');
  const sideNextBtn = document.getElementById('flip-side-next');

  let pageFlip = null;
  let totalPages = 0;
  let zoomLevel = 1;
  let mixedDimensionsDetected = false;

  const updateZoom = (nextZoom) => {
    zoomLevel = Math.min(1.8, Math.max(0.75, Math.round(nextZoom * 100) / 100));
    if (stageWrapper) {
      stageWrapper.style.transform = `scale(${zoomLevel})`;
    }
    if (zoomLabel) {
      zoomLabel.textContent = `${Math.round(zoomLevel * 100)}%`;
    }
  };

  const syncPageUi = (zeroBasedIndex) => {
    const current = Math.min(totalPages, Math.max(1, (zeroBasedIndex || 0) + 1));
    if (pageInput) pageInput.value = String(current);
    if (btnFirst) btnFirst.disabled = current <= 1;
    if (btnPrev) btnPrev.disabled = current <= 1;
    if (sidePrevBtn) sidePrevBtn.disabled = current <= 1;
    if (btnNext) btnNext.disabled = current >= totalPages;
    if (btnLast) btnLast.disabled = current >= totalPages;
    if (sideNextBtn) sideNextBtn.disabled = current >= totalPages;

    if (thumbDrawer) {
      thumbDrawer.querySelectorAll('[data-thumb-page]').forEach((btn) => {
        const idx = Number(btn.getAttribute('data-thumb-page'));
        const isActive = idx === zeroBasedIndex;
        btn.classList.toggle('ring-2', isActive);
        btn.classList.toggle('ring-[#04B1C7]', isActive);
        btn.classList.toggle('opacity-70', !isActive);
      });
    }
  };

  const loadPdfAndRenderFlipbook = async () => {
    try {
      const loadingTask = pdfjsLib.getDocument({
        url: pdfUrl,
        withCredentials: false,
      });

      const pdf = await loadingTask.promise;
      totalPages = pdf.numPages;
      if (pageTotalEl) pageTotalEl.textContent = `/ ${totalPages}`;
      if (pageInput) pageInput.max = String(totalPages);

      const firstPage = await pdf.getPage(1);
      const firstVp = firstPage.getViewport({ scale: 1 });
      const baseAspect = firstVp.width / Math.max(1, firstVp.height);

      // Compute clean reference page dimensions within the stage (fit viewport cleanly)
      const targetHeight = baseAspect >= 1 ? 480 : 540;
      const pageWidth = Math.round(targetHeight * baseAspect);
      const pageHeight = Math.round(targetHeight);
      const renderScale = Math.min(2.4, Math.max(1.75, window.devicePixelRatio || 1.75)) * (pageWidth / Math.max(1, firstVp.width));

      const pageElements = [];
      bookEl.innerHTML = '';
      if (thumbDrawer) thumbDrawer.innerHTML = '';

      for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
        if (loaderText) {
          loaderText.textContent = `Rendering catalog page ${pageNum} of ${totalPages}...`;
        }
        if (loaderBar) {
          loaderBar.style.width = `${Math.round((pageNum / totalPages) * 100)}%`;
        }

        const page = pageNum === 1 ? firstPage : await pdf.getPage(pageNum);
        const vp = page.getViewport({ scale: 1 });
        const pageAspect = vp.width / Math.max(1, vp.height);
        if (Math.abs(pageAspect - baseAspect) > 0.03) {
          mixedDimensionsDetected = true;
        }

        const scaledVp = page.getViewport({ scale: renderScale });
        const canvas = document.createElement('canvas');
        canvas.width = Math.floor(scaledVp.width);
        canvas.height = Math.floor(scaledVp.height);
        canvas.className = 'w-full h-full object-contain bg-white block select-none';

        const ctx = canvas.getContext('2d', { alpha: false });
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        await page.render({
          canvasContext: ctx,
          viewport: scaledVp,
        }).promise;

        const pageWrapper = document.createElement('div');
        pageWrapper.className = 'flip-book-page bg-white overflow-hidden shadow-inner flex items-center justify-center';
        pageWrapper.setAttribute('data-density', pageNum === 1 || pageNum === totalPages ? 'hard' : 'soft');
        pageWrapper.appendChild(canvas);
        bookEl.appendChild(pageWrapper);
        pageElements.push(pageWrapper);

        // Create crisp thumbnail button cleanly using smaller scaled canvas
        if (thumbDrawer) {
          const thumbBtn = document.createElement('button');
          thumbBtn.type = 'button';
          thumbBtn.setAttribute('data-thumb-page', String(pageNum - 1));
          thumbBtn.className = 'shrink-0 flex flex-col items-center gap-1 p-1.5 rounded-lg bg-white/10 hover:bg-white/20 transition-all cursor-pointer opacity-70 hover:opacity-100';

          const thumbCanvas = document.createElement('canvas');
          const thumbScale = 110 / Math.max(1, vp.height);
          const thumbVp = page.getViewport({ scale: thumbScale });
          thumbCanvas.width = Math.floor(thumbVp.width);
          thumbCanvas.height = Math.floor(thumbVp.height);
          thumbCanvas.className = 'rounded border border-white/20 bg-white block';
          const thumbCtx = thumbCanvas.getContext('2d', { alpha: false });
          thumbCtx.fillStyle = '#ffffff';
          thumbCtx.fillRect(0, 0, thumbCanvas.width, thumbCanvas.height);
          await page.render({ canvasContext: thumbCtx, viewport: thumbVp }).promise;

          const labelSpan = document.createElement('span');
          labelSpan.className = 'text-[11px] font-semibold text-white/85';
          labelSpan.textContent = String(pageNum);

          thumbBtn.appendChild(thumbCanvas);
          thumbBtn.appendChild(labelSpan);
          thumbBtn.addEventListener('click', () => {
            if (pageFlip) pageFlip.flip(pageNum - 1);
          });
          thumbDrawer.appendChild(thumbBtn);
        }
      }

      if (mixedDimensionsDetected) {
        const noticeEl = document.getElementById('flip-dimension-warning');
        if (noticeEl) noticeEl.classList.remove('hidden');
      }

      const updateBookCenter = (zeroBasedIndex, animate = true) => {
        if (!bookEl || !pageFlip) return;

        bookEl.style.transition = animate
          ? 'transform 420ms cubic-bezier(0.22, 1, 0.36, 1)'
          : 'none';

        const isLandscape = pageFlip.getOrientation() === 'landscape';
        const hasCoverSpread = totalPages > 2;

        if (!isLandscape || !hasCoverSpread) {
          bookEl.style.transform = 'translateX(0%)';
          return;
        }

        if (zeroBasedIndex === 0) {
          // Front cover sits on the right half of a 2-page spread
          bookEl.style.transform = 'translateX(-25%)';
        } else if (totalPages % 2 === 0 && zeroBasedIndex >= totalPages - 1) {
          // Even-count back cover sits alone on the left half of the final spread
          bookEl.style.transform = 'translateX(25%)';
        } else {
          bookEl.style.transform = 'translateX(0%)';
        }
      };

      const isMobile = window.innerWidth < 768;
      pageFlip = new PageFlip(bookEl, {
        width: pageWidth,
        height: pageHeight,
        size: 'stretch',
        minWidth: 260,
        maxWidth: Math.max(pageWidth + 140, 720),
        minHeight: 340,
        maxHeight: Math.max(pageHeight + 140, 920),
        maxShadowOpacity: 0.42,
        showCover: totalPages > 2,
        mobileScrollSupport: true,
        usePortrait: isMobile || totalPages === 1,
        drawShadow: true,
        flippingTime: 750,
        swipeDistance: 30,
      });

      pageFlip.loadFromHTML(pageElements);
      updateBookCenter(0, false);

      pageFlip.on('flip', (e) => {
        syncPageUi(e.data);
        updateBookCenter(e.data, true);
      });

      pageFlip.on('changeOrientation', () => {
        updateBookCenter(pageFlip.getCurrentPageIndex(), false);
      });

      if (loaderEl) loaderEl.classList.add('hidden');
      if (stageWrapper) stageWrapper.classList.remove('opacity-0');
      syncPageUi(0);

      // Wire toolbar + side arrows + keyboard navigation
      btnFirst?.addEventListener('click', () => pageFlip.flip(0));
      btnPrev?.addEventListener('click', () => pageFlip.flipPrev());
      btnNext?.addEventListener('click', () => pageFlip.flipNext());
      btnLast?.addEventListener('click', () => pageFlip.flip(Math.max(0, totalPages - 1)));
      sidePrevBtn?.addEventListener('click', () => pageFlip.flipPrev());
      sideNextBtn?.addEventListener('click', () => pageFlip.flipNext());

      pageInput?.addEventListener('change', () => {
        const target = parseInt(pageInput.value, 10);
        if (!Number.isNaN(target) && target >= 1 && target <= totalPages) {
          pageFlip.flip(target - 1);
        } else {
          syncPageUi(pageFlip.getCurrentPageIndex());
        }
      });

      zoomInBtn?.addEventListener('click', () => updateZoom(zoomLevel + 0.15));
      zoomOutBtn?.addEventListener('click', () => updateZoom(zoomLevel - 0.15));
      zoomLabel?.addEventListener('click', () => updateZoom(1));

      thumbToggleBtn?.addEventListener('click', () => {
        thumbDrawer?.classList.toggle('hidden');
      });

      fullscreenBtn?.addEventListener('click', () => {
        if (!fullscreenTarget) return;
        if (!document.fullscreenElement) {
          fullscreenTarget.requestFullscreen?.();
        } else {
          document.exitFullscreen?.();
        }
      });

      window.addEventListener('keydown', (e) => {
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName || '')) return;
        if (e.key === 'ArrowRight') pageFlip.flipNext();
        if (e.key === 'ArrowLeft') pageFlip.flipPrev();
      });
    } catch (err) {
      console.error('Failed to render Partner Reward Catalog PDF:', err);
      if (loaderEl) loaderEl.classList.add('hidden');
      if (errorEl) errorEl.classList.remove('hidden');
    }
  };

  loadPdfAndRenderFlipbook();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPartnerRewardFlipbook, { once: true });
} else {
  initPartnerRewardFlipbook();
}
