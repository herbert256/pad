<?php

  // {widget:contextmenu 'Actions for report.pdf'}<a href="?open">Open</a>...{/widget:contextmenu} - a menu
  // of the actions on a part of the page: a right click on the part opens it at the pointer,
  // the context menu key or Shift+F10 at the focused element, and a small button of three
  // dots beside it opens the same menu - a popover, so the button works without a script.
  // The part is the element the menu stands in, or with area= the nearest element around it
  // the selector names - area='tr' for a table row. The first parameter names the menu and
  // its button. A small script (CSP nonce) adds the right click, the keys and the arrow keys
  // between the items. subs/widgets/_lib/contextmenu.php.
  //
  // Like {pad:live} it runs twice: the content renders first, then the end walk puts it in the
  // menu.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {widget:contextmenu} never closes" );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padCtxName  = is_string ( $padParm ) ? trim ( padParmText ( $padParm ) ) : '';
  $padCtxItems = trim ( $padContent );
  $padCtxArea  = padContextmenuArea ();

  if ( $padCtxName === '' or $padCtxItems === '' ) {

    if ( $padCheckSyntax )
      padError ( $padCtxName === ''
        ? "the {widget:contextmenu} needs its name - {widget:contextmenu 'Actions for report.pdf'}...{/widget:contextmenu}"
        : "the {widget:contextmenu '" . padMakeSafe ( $padCtxName, 30 ) . "'} has nothing in its menu - links or buttons between its tags" );

    $padContent = '';

    return TRUE;

  }

  $padContent = padContextmenu ( $padCtxName, $padCtxItems, $padCtxArea );

  return TRUE;

?>
