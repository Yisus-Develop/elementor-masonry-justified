(function(){
  /* ========= util ========= */
  function collectItems(container){
    let items = Array.from(container.querySelectorAll('.emj-item'));
    if (!items.length){
      items = Array.from(container.querySelectorAll('.emj-row .emj-item'));
    }
    return items;
  }
  function restoreItems(container){
    const items = collectItems(container);
    if (!items.length) return items;
    const frag = document.createDocumentFragment();
    items.forEach((it)=>{
      it.style.width=''; it.style.height='';
      frag.appendChild(it);
    });
    container.querySelectorAll('.emj-row').forEach((r)=>{ r.remove(); });
    container.appendChild(frag);
    return items;
  }

  /* ========= justified ========= */
  function layoutJustified(container, cfg){
    const gap = +cfg.gap || 6;
    const target = +cfg.target || 360;
    const lastRow = cfg.lastRow || 'center';
    const factorTablet = +cfg.factorTablet || 0.85;
    const factorMobile = +cfg.factorMobile || 0.7;

    const vw = container.clientWidth;
    if (vw <= 0) return;

    const items = restoreItems(container);
    if (!items.length) return;

    let rowH = target;
    if (vw <= 768) rowH = Math.max(120, Math.round(target * factorMobile));
    else if (vw <= 1024) rowH = Math.max(140, Math.round(target * factorTablet));

    let row = [];
    let rowWidthAtTarget = 0;
    const totalWidth = vw;

    function flushRow(isLast){
      if (!row.length) return;
      const gapsTotal = gap * (row.length - 1);
      let scale = (totalWidth - gapsTotal) / rowWidthAtTarget;
      if (isLast){
        if (lastRow === 'center') scale = 1;
        else if (lastRow === 'hide' && (rowWidthAtTarget + gapsTotal)/totalWidth < 0.8){
          row=[]; rowWidthAtTarget=0; return;
        }
      }
      const rowEl = document.createElement('div');
      rowEl.className = 'emj-row';
      rowEl.style.setProperty('--gap', gap + 'px');
      row.forEach((it)=>{
        const r = +it.getAttribute('data-ratio') || 1.5;
        const h = Math.max(40, rowH * scale);
        const w = Math.max(40, r * h);
        it.style.width  = w + 'px';
        it.style.height = h + 'px';
        rowEl.appendChild(it);
      });
      container.appendChild(rowEl);
      row=[]; rowWidthAtTarget=0;
    }

    items.forEach((it)=>{
      const ratio = +it.getAttribute('data-ratio') || 1.5;
      const wAtTarget = ratio * rowH;
      const gapsTotal = gap * row.length;
      if (rowWidthAtTarget + wAtTarget + gapsTotal > totalWidth && row.length) flushRow(false);
      row.push(it); rowWidthAtTarget += wAtTarget;
    });
    flushRow(true);
  }

  /* ========= mosaic ========= */
  function layoutMosaic(container, cfg){
    const gap = +cfg.gap || 6;
    const baseRow = +cfg.spanTarget || 320;
    const factor  = +cfg.spanFactor || 2;
    const minSide = Math.max(1, +cfg.spanMinSide || 2);
    const spanEvery = Math.max(1, +cfg.spanEvery || 3);

    const vw = container.clientWidth;
    if (vw <= 0) return;

    const pool = restoreItems(container);
    if (!pool.length) return;

    const totalWidth = container.clientWidth;
    let idx=0, portraitCounter=0;

    while (idx < pool.length){
      const big = pool[idx];
      const isPortrait = big.getAttribute('data-portrait') === '1';
      let useMosaicRow = false;

      if (isPortrait) portraitCounter++;
      if (isPortrait && (portraitCounter % spanEvery === 0) && (idx + minSide) < pool.length){
        useMosaicRow = true;
      }

      if (!useMosaicRow){
        const rowEl = document.createElement('div');
        rowEl.className = 'emj-row';
        rowEl.style.setProperty('--gap', gap + 'px');

        let row = [], rowWidthAtTarget = 0;
        while (idx < pool.length){
          const it  = pool[idx];
          const rat = +it.getAttribute('data-ratio') || 1.5;
          const wAt = rat * baseRow;
          const gaps = gap * row.length;
          if (rowWidthAtTarget + wAt + gaps > totalWidth && row.length) break;
          row.push(it); rowWidthAtTarget += wAt; idx++;
        }
        if (!row.length){ row.push(pool[idx++]); }

        const gapsTotal = gap * (row.length - 1);
        const scale = (totalWidth - gapsTotal) / rowWidthAtTarget;

        row.forEach((it)=>{
          const r = +it.getAttribute('data-ratio') || 1.5;
          const h = Math.max(40, baseRow * scale);
          const w = Math.max(40, r * h);
          it.style.width  = w + 'px';
          it.style.height = h + 'px';
          rowEl.appendChild(it);
        });
        container.appendChild(rowEl);
      } else {
        const rowEl2 = document.createElement('div');
        rowEl2.className = 'emj-row mosaic';
        rowEl2.style.setProperty('--gap', gap + 'px');

        let bigRatio = +big.getAttribute('data-ratio') || 0.7;
        let bigH = baseRow * factor;
        let bigW = bigRatio * bigH;

        const side = [];
        let sumInv = 0, j = 1;
        while (side.length < minSide && (idx + j) < pool.length){
          const it2 = pool[idx + j];
          side.push(it2);
          const r2 = +it2.getAttribute('data-ratio') || 1.5;
          sumInv += (1 / r2);
          j++;
        }
        if (!side.length){ portraitCounter = 0; continue; }

        const gapsColumn = gap * (side.length - 1);
        let Wside = Math.max(40, (bigH - gapsColumn) / sumInv);

        const scale2 = (totalWidth - gap) / (bigW + Wside);
        bigH *= scale2; bigW *= scale2; Wside *= scale2;

        const bigWrap = document.createElement('div');
        bigWrap.className = 'emj-big';
        bigWrap.style.width  = bigW + 'px';
        bigWrap.style.height = bigH + 'px';

        big.style.width='100%'; big.style.height='100%';
        bigWrap.appendChild(big);

        const col = document.createElement('div');
        col.className = 'emj-col';
        col.style.width = Wside + 'px';

        side.forEach((it3)=>{
          const r3 = +it3.getAttribute('data-ratio') || 1.5;
          const h3 = Math.max(40, Wside / r3);
          it3.style.width  = Wside + 'px';
          it3.style.height = h3 + 'px';
          col.appendChild(it3);
        });

        rowEl2.appendChild(bigWrap);
        rowEl2.appendChild(col);
        container.appendChild(rowEl2);

        idx += (1 + side.length);
      }
    }
  }

  /* ========= Carousel (Swiper) ========= */
  function initCarousel(wrap, cfg){
    if (typeof Swiper === 'undefined') {
        console.warn('EMJ: Swiper not defined');
        return;
    }
    if (wrap.swiper) return;
    
    const swCfg = {
        slidesPerView: +cfg.carousel.slidesPerView || 3,
        spaceBetween: +cfg.gap || 10,
        loop: !!cfg.carousel.loop,
        autoplay: cfg.carousel.autoplay ? { delay: +cfg.carousel.autoplay, disableOnInteraction: false } : false,
        navigation: cfg.carousel.arrows ? { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' } : false,
        pagination: cfg.carousel.pagination ? { el: '.swiper-pagination', clickable: true } : false,
        breakpoints: {
            0: { slidesPerView: +cfg.carousel.slidesPerViewMobile || 1 },
            768: { slidesPerView: +cfg.carousel.slidesPerViewTablet || 2 },
            1024: { slidesPerView: +cfg.carousel.slidesPerView || 3 }
        }
    };

    new Swiper(wrap, swCfg);
  }

  /* ========= GLightbox ========= */
  function initGLightbox(){
    if (typeof GLightbox === 'undefined') return;
    GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });
  }

  function doLayout(wrap){
    let cfg = {};
    try { cfg = JSON.parse(wrap.getAttribute('data-emj') || '{}'); } catch(e){}
    
    console.log('EMJ: doLayout', cfg.layout, wrap.id);
    
    if (cfg.layout === 'carousel') {
        initCarousel(wrap, cfg);
    } else {
        if (cfg.mode === 'justified') layoutJustified(wrap, cfg);
        else layoutMosaic(wrap, cfg);
    }
  }

  function boot(){
    document.querySelectorAll('.emj-wrap, .emj-carousel').forEach((wrap)=>{
      if (wrap.dataset.bound) return;
      wrap.dataset.bound = '1';

      const relayout = ()=>{
        doLayout(wrap);
        wrap.classList.add('emj-ready');
      };
      
      relayout();

      const inEditor = document.body.classList.contains('elementor-editor-active') ||
                       (window.elementorFrontend && elementorFrontend.isEditMode && elementorFrontend.isEditMode());

      if (inEditor) {
        let debounce;
        const trigger = ()=>{ clearTimeout(debounce); debounce = setTimeout(relayout, 120); };
        new MutationObserver(trigger).observe(document.body, {attributes:true, attributeFilter:['class']});
        try {
          if (window.elementorFrontend && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction('panel/open_editor', trigger);
            elementorFrontend.hooks.addAction('panel/close_editor', trigger);
            elementorFrontend.hooks.addAction('frontend/element_ready/global', trigger);
          }
        } catch(e){}
        [80, 300, 900].forEach((ms)=> setTimeout(relayout, ms));
      }

      let t;
      window.addEventListener('resize', ()=>{
        clearTimeout(t); t = setTimeout(relayout, 120);
      });

      wrap.querySelectorAll('img').forEach((img)=>{
        if (img.complete) return;
        img.addEventListener('load', relayout, {once:true});
        img.addEventListener('error', relayout, {once:true});
      });
    });

    initGLightbox();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();
})();
