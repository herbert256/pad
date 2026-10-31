<?php

  // {tab 'Overview'} ... {/tab} - one item of the {tabs}, {accordion}, {carousel} or
  // {wizard} it stands in: its label and its content, rendered and handed to that owner
  // instead of printed here (widgets/_lib/tabs.php). It may stand deeper than directly
  // inside - in an {if}, or in a loop that makes a tab per row. A tab of a set, an accordion
  // or a wizard needs its label; a slide of a carousel may go without, its label is its
  // caption.
  //
  // The owners are widgets (widgets/types/), the item a built-in tag: it belongs to several
  // of them, and a built-in tag comes before a field of its name - a row with a field named
  // tab, {settings}{tab $group}...{/tab}{/settings}, still makes tabs - where a widget
  // reached bare comes after every field.

  include_once PAD . 'widgets/_lib/tabs.php';

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {tab} never closes" );

    if ( padTabsOwner () === FALSE ) {
      if ( $padCheckSyntax )
        padError ( "a {tab} belongs inside a {tabs}, an {accordion} or a {carousel} - or a {wizard}" );
      return FALSE;
    }

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padTabOwner = padTabsOwner ();
  $padTabLabel = trim ( padParmText ( $padParm ) );

  $padTabKind  = padTabsKind ( $padTabOwner );

  if ( $padTabLabel === '' and $padTabKind != 'carousel' and $padCheckSyntax )
    padError ( "a {tab} of " . ( $padTabKind == 'accordion' ? 'an' : 'a' ) . " {" . $padTabKind . "} needs its label - {tab 'Overview'}" );

  padTabsAdd ( $padTabOwner, $padTabLabel, trim ( $padContent ) );

  $padContent = '';

  return TRUE;

?>
