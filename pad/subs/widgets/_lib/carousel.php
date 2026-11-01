<?php

  // A carousel without JavaScript - the carousel widget: every {tab} a slide of a row that
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
  // The items are collected the way {tabs} collects its tabs - subs/widgets/_lib/tabs.php.

  include_once PAD . 'subs/widgets/_lib/tabs.php';

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

  // The rules once per page - assets/carousel.css, which holds the marks of the dots for
  // twelve slides a carousel; one of more adds the marks of its further slides, each once.

  function padCarouselStyle ( $count ) {

    $css = padWidgetStyle ( 'carousel',
      [ 'accent'  => [ '#2a78d6', '#5598e7' ],
        'text'    => [ '#1f1f1d', '#ecebe6' ],
        'muted'   => [ '#62615c', '#a9a8a0' ],
        'surface' => [ '#fcfcfb', '#1a1a19' ],
        'dot'     => [ '#c9c8c2', '#4a4a46' ] ],
      padWidgetAsset ( 'carousel.css' ) );

    for ( $n = 13; $n <= $count; $n++ )
      if ( padWidgetOnce ( "carousel-mark:$n" ) )
        $css .= padProtect ( '<style' . padStyleNonce () . '>' . padCarouselMark ( $n ) . '</style>' );

    return $css;

  }

  function padCarouselMark ( $n ) {

    return ".pad-carousel:not([data-pad-current]):has(>.pad-carousel-track>.pad-carousel-slide:nth-child($n):target)>.pad-carousel-dots>.pad-carousel-dot:nth-child($n){background:var(--pad-carousel-accent)}";

  }

  // The script: a link of a carousel scrolls its row, not the page; the row's position says
  // which dot is current, after a click, a swipe or a scroll by the keyboard. A carousel
  // reaches its own row and dots only - the outer one of two marked the inner one's dots,
  // and its CSS did the same.

  function padCarouselScript () {

    return padWidgetScript ( 'carousel', padWidgetAsset ( 'carousel.js' ) );

  }

?>
