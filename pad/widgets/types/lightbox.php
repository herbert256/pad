<?php

  // {lightbox label='The harbour'}{tab 'At dawn'}<img src="dawn.jpg" alt="...">{/tab}...{/lightbox}
  // - a gallery: every {tab} a thumbnail in a grid, a link that opens the picture large over
  // the page, with its caption, the pictures before and after it and a close link - the
  // targeted view shown by CSS (:target), so no script is needed; a small script (CSP nonce)
  // adds Escape and the arrow keys. label= names the gallery for a screen reader ('Gallery');
  // a picture's label is its caption, and may be left out. widgets/_lib/lightbox.php; the
  // {tab}s are collected as {tabs} collects them, widgets/_lib/tabs.php.
  //
  // Like {live} it runs twice: first it opens the collection its {tab}s hand their content
  // to and asks for the end walk, then it builds the gallery from what they handed over.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {lightbox} never closes" );

    padTabsOpen ( 'lightbox' );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padBoxItems = padTabsItems ( 'lightbox', $padContent );
  $padBoxLabel = padWidgetText ( 'label', 'Gallery' );

  $padContent = $padBoxItems ? padLightbox ( $padBoxItems, $padBoxLabel !== '' ? $padBoxLabel : 'Gallery' ) : '';

  return TRUE;

?>
