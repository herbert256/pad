<?php

  // {widget:carousel label='Our kettles'}{pad:tab 'Sunrise'}...{/pad:tab}{pad:tab 'Ocean'}...{/pad:tab}{/widget:carousel} -
  // every {pad:tab} a slide of a row that scrolls and snaps sideways, with previous and next links
  // on each slide and a dot per slide below: links to the slides' ids, so no JavaScript is
  // needed - a small script (CSP nonce) only keeps the page still and marks the current dot.
  // No autoplay. label= names the carousel for a screen reader ('Carousel' when not given); a
  // slide's label is its caption. subs/widgets/_lib/carousel.php; the {pad:tab}s are collected as {widget:tabs}
  // collects them, subs/widgets/_lib/tabs.php.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {widget:carousel} never closes" );

    padTabsOpen ( 'carousel' );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padCarItems = padTabsItems ( 'carousel', $padContent );
  $padCarLabel = trim ( padParmText ( padTagParm ( 'label', 'Carousel' ) ) );

  $padContent = $padCarItems ? padCarousel ( $padCarItems, $padCarLabel !== '' ? $padCarLabel : 'Carousel' ) : '';

  return TRUE;

?>
