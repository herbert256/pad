<?php

  // {sortable 'priority'}{tab 'a1'}Write the report{/tab}{tab 'b7'}Call the bank{/tab}{/sortable}
  // - a short list put in order by hand, its order posted with the form it stands in: every
  // {tab} an item, its parameter the key the item is posted under - priority[a1]=1,
  // priority[b7]=2, the place from 1. Without scripting each item has a number field, its
  // place; with scripting a small script (CSP nonce) gives it a grip to drag it by, which
  // Space, the arrow keys and Escape work as well, and Move up and Move down buttons, and
  // says every move in a live region. The first parameter is the field's name, label= names
  // the list for a screen reader (the name made readable). A form that comes back keeps the
  // order posted. subs/widgets/_lib/sortable.php; the {tab}s are collected as {tabs}
  // collects them, subs/widgets/_lib/tabs.php.
  //
  // Like {live} it runs twice: first it opens the collection its {tab}s hand their content
  // to and asks for the end walk, then it builds the list from what they handed over.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {sortable} never closes" );

    if ( padWidgetName ( 'sortable', "{sortable 'priority'}" ) === FALSE )
      return FALSE;

    padTabsOpen ( 'sortable' );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padSortItems = padTabsItems ( 'sortable', $padContent );
  $padSortLabel = padWidgetText ( 'label', '' );

  if ( $padSortLabel === '' )
    $padSortLabel = ucfirst ( strtolower ( padStrHeadline ( (string) $padParm ) ) );

  if ( ! $padSortItems or ! padSortableKeys ( $padSortItems ) ) {

    $padContent = implode ( '', array_column ( $padSortItems, 'content' ) );

    return TRUE;

  }

  $padContent = padSortable ( (string) $padParm, padSortableBack ( (string) $padParm, $padSortItems ), $padSortLabel );

  return TRUE;

?>
