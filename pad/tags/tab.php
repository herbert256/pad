<?php

  // {tab 'Overview'} ... {/tab} - one item of the {tabs}, {accordion} or {carousel} it
  // stands in: its label and its content, rendered and handed to that owner instead of
  // printed here (lib/tabs.php). It may stand deeper than directly inside - in an {if}, or
  // in a loop that makes a tab per row. A tab of a set or an accordion needs its label; a
  // slide of a carousel may go without, its label is its caption.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {tab} never closes" );

    if ( padTabsOwner () === FALSE ) {
      if ( $padCheckSyntax )
        padError ( "a {tab} belongs inside a {tabs}, an {accordion} or a {carousel}" );
      return FALSE;
    }

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padTabOwner = padTabsOwner ();
  $padTabLabel = trim ( (string) $padParm );

  $padTabKind  = padTabsKind ( $padTabOwner );

  if ( $padTabLabel === '' and $padTabKind != 'carousel' and $padCheckSyntax )
    padError ( "a {tab} of " . ( $padTabKind == 'accordion' ? 'an' : 'a' ) . " {" . $padTabKind . "} needs its label - {tab 'Overview'}" );

  padTabsAdd ( $padTabOwner, $padTabLabel, trim ( $padContent ) );

  $padContent = '';

  return TRUE;

?>
