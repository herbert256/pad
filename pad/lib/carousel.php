<?php

  // A carousel without JavaScript - the {carousel} tag: every {tab} a slide of a row that
  // scrolls sideways and snaps to a slide (CSS scroll snap), so a finger, a trackpad or the
  // arrow keys on the focused row move it. Each slide carries a link to the slide before and
  // after it, and below the row a dot links to every slide: links to the slides' ids, which
  // the browser scrolls into view - no script, and the address names the slide shown.
  //
  //   {carousel label='Our kettles'}
  //     {tab 'Sunrise'}<svg ...>...</svg>{/tab}
  //     {tab 'Ocean'}<img src="ocean.jpg" alt="...">{/tab}
  //   {/carousel}
  //
  // The markup follows the WAI-ARIA carousel pattern: the region says it is a carousel and
  // is named by label=, every slide is a group saying 'slide 2 of 4' with its label. A
  // slide's label is its caption under the slide; it may go without one. There is no
  // autoplay - moving content a visitor did not ask for is an accessibility fault - and
  // the scrolling is smooth only when no reduced motion is asked for.
  //
  // Without scripting the dot of the slide a link went to is marked (:target, through :has),
  // and a link to a slide also scrolls the page to it, as a link to an id does. A small
  // script, once per page with the CSP nonce, makes it smoother where it can run: the links
  // scroll the row alone, and the dot of the slide in view - also one reached by swiping -
  // is marked, aria-current for a screen reader.
  //
  // The items are collected the way {tabs} collects its tabs - lib/tabs.php.

  function padCarousel ( $items, $label ) {

    $id    = padWidgetId ( 'carousel', [ $label, array_column ( $items, 'label' ), count ( $items ) ] );
    $count = count ( $items );
    $show  = padWidgetAttr ( $label );
    $out   = $dots = '';

    foreach ( $items as $index => $item ) {

      $n    = $index + 1;
      $prev = ( $n == 1 ) ? $count : $n - 1;
      $next = ( $n == $count ) ? 1 : $n + 1;
      $name = padWidgetAttr ( $item ['label'] );
      $says = "$n of $count" . ( $name !== '' ? ": $name" : '' );

      $out .= padProtect ( "<div class=\"pad-carousel-slide\" id=\"$id-$n\" role=\"group\" aria-roledescription=\"slide\" aria-label=\"$says\">"
                         . '<div class="pad-carousel-content">' )
            . $item ['content']
            . padProtect ( '</div>'
                         . ( $name !== '' ? "<p class=\"pad-carousel-caption\">$name</p>" : '' )
                         . ( $count > 1 ? "<a class=\"pad-carousel-prev\" href=\"#$id-$prev\" aria-label=\"Previous slide\"><span aria-hidden=\"true\">&#8249;</span></a>"
                                        . "<a class=\"pad-carousel-next\" href=\"#$id-$next\" aria-label=\"Next slide\"><span aria-hidden=\"true\">&#8250;</span></a>" : '' )
                         . '</div>' );

      $dots .= "<a class=\"pad-carousel-dot\" href=\"#$id-$n\" aria-label=\"Slide $says\"></a>";

    }

    return padCarouselStyle ( $count )
         . padProtect ( "<section class=\"pad-carousel\" id=\"$id\" aria-roledescription=\"carousel\" aria-label=\"$show\">"
                      . "<div class=\"pad-carousel-track\" tabindex=\"0\" aria-label=\"$show, slides\">" )
         . $out
         . padProtect ( '</div>'
                      . ( $count > 1 ? "<nav class=\"pad-carousel-dots\" aria-label=\"$show, choose a slide\">$dots</nav>" : '' )
                      . '</section>' )
         . padCarouselScript ();

  }

  // The rules once per page, the marks of the dots for twelve slides a carousel; one of more
  // adds the marks of its further slides, each once.

  function padCarouselStyle ( $count ) {

    $marks = '';

    for ( $n = 1; $n <= 12; $n++ )
      $marks .= padCarouselMark ( $n );

    $css = padWidgetStyle ( 'carousel',
      [ 'accent'  => [ '#2a78d6', '#5598e7' ],
        'text'    => [ '#1f1f1d', '#ecebe6' ],
        'muted'   => [ '#62615c', '#a9a8a0' ],
        'surface' => [ '#fcfcfb', '#1a1a19' ],
        'dot'     => [ '#c9c8c2', '#4a4a46' ] ],
      '.pad-carousel{position:relative;margin:0 0 1em}'
      . '.pad-carousel-track{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;overscroll-behavior-x:contain;'
      .   'scrollbar-width:none;border-radius:12px}'
      . '.pad-carousel-track::-webkit-scrollbar{display:none}'
      . '.pad-carousel-track:focus-visible{outline:2px solid var(--pad-carousel-accent);outline-offset:2px}'
      . '@media (prefers-reduced-motion:no-preference){.pad-carousel-track{scroll-behavior:smooth}}'
      . '.pad-carousel-slide{position:relative;flex:0 0 100%;scroll-snap-align:start;scroll-snap-stop:always;margin:0}'
      . '.pad-carousel-content>*{display:block;width:100%;height:auto}'
      . '.pad-carousel-caption{margin:.5em 0 0;text-align:center;color:var(--pad-carousel-muted);font-size:.9em}'
      . '.pad-carousel-prev,.pad-carousel-next{position:absolute;top:calc(50% - 1.2em);display:flex;align-items:center;'
      .   'justify-content:center;width:2.4em;height:2.4em;border-radius:50%;background:var(--pad-carousel-surface);'
      .   'color:var(--pad-carousel-text);font-size:1.1em;line-height:1;text-decoration:none;opacity:.85;'
      .   'box-shadow:0 1px 4px rgba(0,0,0,.25)}'
      . '.pad-carousel-prev{left:.6em}.pad-carousel-next{right:.6em}'
      . '.pad-carousel-prev span,.pad-carousel-next span{font-size:1.6em;margin-top:-.15em}'
      . '.pad-carousel-prev:hover,.pad-carousel-next:hover{opacity:1}'
      . '.pad-carousel-dots{display:flex;justify-content:center;gap:.5em;padding:.7em 0 0}'
      . '.pad-carousel-dot{width:.7em;height:.7em;border-radius:50%;background:var(--pad-carousel-dot)}'
      . '.pad-carousel-dot:hover{background:var(--pad-carousel-muted)}'
      . '.pad-carousel a:focus-visible{outline:2px solid var(--pad-carousel-accent);outline-offset:2px}'
      . '.pad-carousel:not([data-pad-current]):not(:has(.pad-carousel-slide:target)) .pad-carousel-dot:first-child,'
      . '.pad-carousel-dot[aria-current]{background:var(--pad-carousel-accent)}'
      . $marks
      . '@media print{.pad-carousel-track{flex-direction:column}.pad-carousel-prev,.pad-carousel-next,.pad-carousel-dots{display:none}}' );

    for ( $n = 13; $n <= $count; $n++ )
      if ( padWidgetOnce ( "carousel-mark:$n" ) )
        $css .= padProtect ( '<style>' . padCarouselMark ( $n ) . '</style>' );

    return $css;

  }

  function padCarouselMark ( $n ) {

    return ".pad-carousel:not([data-pad-current]):has(>.pad-carousel-track>.pad-carousel-slide:nth-child($n):target) .pad-carousel-dot:nth-child($n){background:var(--pad-carousel-accent)}";

  }

  // The script: a link of a carousel scrolls its row, not the page; the row's position says
  // which dot is current, after a click, a swipe or a scroll by the keyboard.

  function padCarouselScript () {

    return padWidgetScript ( 'carousel', <<<'SCRIPT'
(function () {
  if (window.padCarouselReady) return;
  window.padCarouselReady = true;
  function mark(carousel) {
    var track = carousel.querySelector('.pad-carousel-track');
    if (!track || !track.clientWidth) return;
    var current = Math.round(track.scrollLeft / track.clientWidth);
    carousel.setAttribute('data-pad-current', current + 1);
    carousel.querySelectorAll('.pad-carousel-dot').forEach(function (dot, index) {
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
SCRIPT );

  }

?>
