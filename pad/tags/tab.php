<?php

  // {pad:tab 'Overview'} ... {/pad:tab} - one item of the {widget:tabs}, {widget:accordion}, {widget:carousel}, {widget:wizard},
  // {widget:lightbox}, {widget:splitpane} or {widget:sortable} it stands in: its label and its content, rendered
  // and handed to that owner instead of printed here (subs/widgets/_lib/tabs.php). It may
  // stand deeper than directly inside - in an {pad:if}, or in a loop that makes a tab per row. A
  // tab of a set, an accordion, a wizard or a split pane needs its label, an item of a
  // sortable its key - the label of its tab; a slide of a carousel and a picture of a
  // lightbox may go without, their label is their caption.
  //
  // The owners are widgets (subs/widgets/types/), the item a built-in tag: it belongs to several
  // of them, and a built-in tag comes before a field of its name - a row with a field named
  // tab, {settings}{pad:tab $group}...{/pad:tab}{/settings}, still makes tabs - where a widget
  // reached bare comes after every field.

  include_once PAD . 'subs/widgets/_lib/tabs.php';

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {pad:tab} never closes" );

    if ( padTabsOwner () === FALSE ) {
      if ( $padCheckSyntax )
        padError ( "a {pad:tab} belongs inside a {widget:tabs}, an {widget:accordion} or a {widget:carousel} - or a {widget:wizard}, a {widget:lightbox}, a {widget:splitpane} or a {widget:sortable}" );
      return FALSE;
    }

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padTabOwner = padTabsOwner ();
  $padTabLabel = trim ( padParmText ( $padParm ) );

  $padTabKind  = padTabsKind ( $padTabOwner );

  if ( $padTabLabel === '' and ! in_array ( $padTabKind, [ 'carousel', 'lightbox' ] ) and $padCheckSyntax )
    padError ( ( $padTabKind == 'sortable' )
      ? "a {pad:tab} of a {widget:sortable} needs its key, which the item is posted under - {pad:tab 'a1'}"
      : "a {pad:tab} of " . ( $padTabKind == 'accordion' ? 'an' : 'a' ) . " {widget:" . $padTabKind . "} needs its label - {pad:tab 'Overview'}" );

  padTabsAdd ( $padTabOwner, $padTabLabel, trim ( $padContent ) );

  $padContent = '';

  return TRUE;

?>
