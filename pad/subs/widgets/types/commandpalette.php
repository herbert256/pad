<?php

  // {commandpalette 'Go to'}<a href="?orders">Orders</a>...{/commandpalette} or
  // {commandpalette 'Go to', data='commands'/} - a search over the commands of an
  // application: a button, and Ctrl+K or ⌘K, open a modal dialog with a search box over the
  // links and buttons between the tags, or the rows of data= - their label, link, group and
  // keywords; typing narrows the list, the arrow keys choose, Enter runs the chosen one. The
  // first parameter is the button's text and the dialog's name ('Commands'); key= the letter
  // of the shortcut (k) - not a, c, v, x, y and z, the keys of editing text, nor n, q, t and w,
  // which the browser keeps - placeholder= the box's hint, empty= what shows when nothing is
  // found. Without scripting the button is a link to the dialog, shown as an overlay with
  // the whole list (:target). A small script (CSP nonce) makes the search.
  // subs/widgets/_lib/commandpalette.php.
  //
  // The pair's content is template, rendered first: like {live}, the pair asks for the end
  // walk and puts what rendered in the list. The rows of data= are read before it renders,
  // and kept for the end walk: reading them gives the level its one default occurrence back,
  // which read at the end came after the content had rendered once for every row.

  if ( $padPair [$pad] and $padWalk [$pad] == 'start' ) {
    $padCmdKept [ $padLevelId [$pad] ] = padCommandpaletteRows ();
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  if ( $padPair [$pad] ) {
    $padCmdRows = $padCmdKept [ $padLevelId [$pad] ] ?? NULL;
    unset ( $padCmdKept [ $padLevelId [$pad] ] );
  } else
    $padCmdRows = padCommandpaletteRows ();

  $padCmdLabel = is_string ( $padParm ) ? trim ( padParmText ( $padParm ) ) : '';
  $padCmdLabel = ( $padCmdLabel !== '' ) ? $padCmdLabel : 'Commands';
  $padCmdItems = trim ( ( $padCmdRows ?? '' ) . ( $padPair [$pad] ? $padContent : '' ) );
  $padCmdKey   = padWidgetChoice ( 'commandpalette', 'key', 'k', str_split ( 'bdefghijklmoprsu' ) );
  $padCmdHint  = padWidgetText ( 'placeholder', 'Type a command' ) ?: 'Type a command';
  $padCmdEmpty = padWidgetText ( 'empty', 'No command found' ) ?: 'No command found';

  if ( $padCmdItems === '' ) {

    if ( $padCheckSyntax )
      padError ( $padCmdRows === NULL
        ? "the {commandpalette} has no commands - links or buttons between its tags, or rows by data="
        : "the {commandpalette} has no commands - no row of its data= has a label and a link" );

    $padContent = '';

    return $padPair [$pad] ? TRUE : '';

  }

  $padCmdHtml = padCommandpalette ( $padCmdLabel, $padCmdItems, $padCmdKey, $padCmdHint, $padCmdEmpty );

  if ( ! $padPair [$pad] )
    return $padCmdHtml;

  $padContent = $padCmdHtml;

  return TRUE;

?>
