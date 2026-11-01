<?php

  // {tab 'Overview'} ... {/tab} - one item of the {tabs}, {accordion}, {carousel}, {wizard},
  // {lightbox} or {splitpane} it stands in: its label and its content, rendered and handed to
  // that owner instead of printed here (subs/widgets/_lib/tabs.php). It may stand deeper than
  // directly inside - in an {if}, or in a loop that makes a tab per row. A tab of a set, an
  // accordion, a wizard or a split pane needs its label; a slide of a carousel and a picture
  // of a lightbox may go without, their label is their caption.
  //
  // The owners are widgets (subs/widgets/types/), the item a built-in tag: it belongs to several
  // of them, and a built-in tag comes before a field of its name - a row with a field named
  // tab, {settings}{tab $group}...{/tab}{/settings}, still makes tabs - where a widget
  // reached bare comes after every field.

  include_once PAD . 'subs/widgets/_lib/tabs.php';

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {tab} never closes" );

    if ( padTabsOwner () === FALSE ) {
      if ( $padCheckSyntax )
        padError ( "a {tab} belongs inside a {tabs}, an {accordion} or a {carousel} - or a {wizard}, a {lightbox} or a {splitpane}" );
      return FALSE;
    }

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padTabOwner = padTabsOwner ();
  $padTabLabel = trim ( padParmText ( $padParm ) );

  $padTabKind  = padTabsKind ( $padTabOwner );

  if ( $padTabLabel === '' and ! in_array ( $padTabKind, [ 'carousel', 'lightbox' ] ) and $padCheckSyntax )
    padError ( "a {tab} of " . ( $padTabKind == 'accordion' ? 'an' : 'a' ) . " {" . $padTabKind . "} needs its label - {tab 'Overview'}" );

  padTabsAdd ( $padTabOwner, $padTabLabel, trim ( $padContent ) );

  $padContent = '';

  return TRUE;

?>
