/**
 * トップのヒーローを数秒ごとに切り替える
 *
 * 1枚目だけを最初に読み込み、残りはページの読み込みが終わってから取りに行く。
 * こうしないと4枚が同時に読まれて、最初の表示が遅くなる。
 * 「視差効果を減らす」設定の端末では切り替えず、1枚目のまま止める。
 */
(function () {
  'use strict';

  var INTERVAL = 4000;

  function init() {
    var slides = document.querySelectorAll('.hero__img');
    var dots = document.querySelectorAll('.hero__dot');

    if (slides.length < 2) {
      return;
    }

    // 2枚目以降をここで読み込む。
    // スマホ用の source を先に有効にしてから img の src を入れる（順番が逆だと横長の方を取りに行く）
    Array.prototype.forEach.call(slides, function (img) {
      var picture = img.parentElement;

      if (picture && picture.tagName === 'PICTURE') {
        Array.prototype.forEach.call(picture.querySelectorAll('source[data-srcset]'), function (source) {
          source.srcset = source.dataset.srcset;
          source.removeAttribute('data-srcset');
        });
      }

      if (img.dataset.src) {
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
      }
    });

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    var current = 0;

    setInterval(function () {
      slides[current].classList.remove('is-active');
      if (dots[current]) {
        dots[current].classList.remove('is-active');
      }

      current = (current + 1) % slides.length;

      slides[current].classList.add('is-active');
      if (dots[current]) {
        dots[current].classList.add('is-active');
      }
    }, INTERVAL);
  }

  if (document.readyState === 'complete') {
    init();
  } else {
    window.addEventListener('load', init);
  }
})();
