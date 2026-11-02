(function () {
  if (window.padCarouselReady) return;
  window.padCarouselReady = true;
  function mark(carousel) {
    var track = carousel.querySelector(':scope > .pad-carousel-track');
    if (!track || !track.clientWidth) return;
    var current = Math.round(track.scrollLeft / track.clientWidth);
    carousel.setAttribute('data-pad-current', current + 1);
    carousel.querySelectorAll(':scope > .pad-carousel-dots > .pad-carousel-dot').forEach(function (dot, index) {
      if (index === current) dot.setAttribute('aria-current', 'true');
      else dot.removeAttribute('aria-current');
    });
  }
  function markAll() {
    document.querySelectorAll('.pad-carousel').forEach(mark);
  }
  document.addEventListener('click', function (e) {
    var link = e.target.closest && e.target.closest('.pad-carousel a[href^="#"]');
    var carousel = link && link.closest('.pad-carousel');
    var slide = carousel && document.getElementById(link.getAttribute('href').slice(1));
    if (!slide || !carousel.contains(slide)) return;
    e.preventDefault();
    var track = slide.parentNode;
    track.scrollTo({ left: Array.prototype.indexOf.call(track.children, slide) * track.clientWidth });
  });
  var pending = false;
  document.addEventListener('scroll', function (e) {
    var track = e.target.classList && e.target.classList.contains('pad-carousel-track') && e.target;
    if (!track || pending) return;
    pending = true;
    requestAnimationFrame(function () { pending = false; mark(track.parentNode); });
  }, true);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', markAll);
  else markAll();
})();
