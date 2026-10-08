<?php

  // {tabs}{tab 'Overview'}...{/tab}{tab 'Specs'}...{/tab}{/tabs} - tabs without JavaScript:
  // a radio button and a label per tab, the CSS shows the panel of the checked one, the
  // arrow keys move between them as in any radio group. active= names the tab shown first,
  // by its number from 1 or its label; the first one otherwise. lib/tabs.php.
  //
  // Like {live} it runs twice: first it opens the collection its {tab}s hand their content
  // to and asks for the end walk, then it builds the tab set from what they handed over.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {tabs} never closes" );

    padTabsOpen ( 'tabs' );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padTabsItems = padTabsItems ( 'tabs', $padContent );
  $padTabsOn    = padTabsPick ( $padTabsItems, padTagParm ( 'active' ), 'tabs', 'active' );

  $padContent = $padTabsItems ? padTabs ( $padTabsItems, $padTabsOn ) : '';

  return TRUE;

?>
