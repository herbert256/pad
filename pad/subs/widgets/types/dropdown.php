<?php

  // {dropdown 'Account'}<a href="?profile">Profile</a>...{/dropdown} - a button and the menu
  // it opens, on the popover attribute: the browser opens and closes the menu without a
  // script - a click outside it or Escape closes it - and places it under its button by CSS
  // anchor positioning. align='end' lines it up with the button's right edge. A small script
  // (CSP nonce) adds the arrow keys between the items and places the menu where the browser
  // cannot. subs/widgets/_lib/dropdown.php.
  //
  // Like {live} it runs twice: the content renders first, then the end walk puts it in the
  // menu.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {dropdown} never closes" );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padDropLabel = is_string ( $padParm ) ? trim ( padParmText ( $padParm ) ) : '';
  $padDropItems = trim ( $padContent );

  if ( $padDropLabel === '' or $padDropItems === '' ) {

    if ( $padCheckSyntax )
      padError ( $padDropLabel === ''
        ? "the {dropdown} needs the text of its button - {dropdown 'Account'}...{/dropdown}"
        : "the {dropdown '" . padMakeSafe ( $padDropLabel, 30 ) . "'} has nothing in its menu - links or buttons between its tags" );

    $padContent = '';

    return TRUE;

  }

  $padContent = padDropdown ( $padDropLabel, $padDropItems, padWidgetChoice ( 'dropdown', 'align', 'start', [ 'start', 'end' ] ) );

  return TRUE;

?>
