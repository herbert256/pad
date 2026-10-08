<?php

  // {accordion}{tab 'Question'}...{/tab}{/accordion} - every {tab} a <details> with its label
  // as the <summary>: opened and closed by the browser, no JavaScript. single lets one item
  // be open at a time (the exclusive accordion of HTML, a shared name); open= names the item
  // open at first - its number from 1 or its label - and open alone opens them all.
  // lib/accordion.php; the {tab}s are collected as {tabs} collects them, lib/tabs.php.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {accordion} never closes" );

    padTabsOpen ( 'accordion' );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padAccItems  = padTabsItems ( 'accordion', $padContent );
  $padAccSingle = (bool) padTagParm ( 'single', FALSE );
  $padAccOpen   = padTagParm ( 'open', '' );

  if ( $padAccOpen === TRUE and $padAccSingle and $padCheckSyntax )
    padError ( "a single {accordion} opens one item at a time - open= names it, by its number or its label" );

  if ( $padAccOpen !== TRUE )
    $padAccOpen = padTabsPick ( $padAccItems, $padAccOpen, 'accordion', 'open' );

  $padContent = $padAccItems ? padAccordion ( $padAccItems, $padAccSingle, $padAccOpen ) : '';

  return TRUE;

?>
