<?php

  // {carousel label='Our kettles'}{tab 'Sunrise'}...{/tab}{tab 'Ocean'}...{/tab}{/carousel} -
  // every {tab} a slide of a row that scrolls and snaps sideways, with previous and next links
  // on each slide and a dot per slide below: links to the slides' ids, so no JavaScript is
  // needed - a small script (CSP nonce) only keeps the page still and marks the current dot.
  // No autoplay. label= names the carousel for a screen reader ('Carousel' when not given); a
  // slide's label is its caption. lib/carousel.php; the {tab}s are collected as {tabs}
  // collects them, lib/tabs.php.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {carousel} never closes" );

    padTabsOpen ( 'carousel' );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padCarItems = padTabsItems ( 'carousel', $padContent );
  $padCarLabel = trim ( (string) padTagParm ( 'label', 'Carousel' ) );

  $padContent = $padCarItems ? padCarousel ( $padCarItems, $padCarLabel !== '' ? $padCarLabel : 'Carousel' ) : '';

  return TRUE;

?>
