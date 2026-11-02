<?php

  // {widget:drawer 'filters', title='Filters', button='Show filters', side='left'} ... {/widget:drawer} - a
  // panel that slides in from the side of the window, with the button that opens it: a modal
  // <dialog> opened as {widget:modal} opens one - by the HTML invoker commands, a small script (CSP
  // nonce) where the browser has none, and a link with a :target overlay where scripting is
  // off. The name is the panel's id - pad-drawer-filters; side= is right (the default), left
  // or bottom; title= heads the panel and names it, button= is the button's text ('Open').
  // subs/widgets/_lib/drawer.php.
  //
  // Like {pad:live} it runs twice: the content renders first, then the end walk puts it in the
  // panel.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {widget:drawer} never closes" );

    if ( padWidgetName ( 'drawer', "{widget:drawer 'filters'}" ) === FALSE )
      return FALSE;

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padDrawButton = padWidgetText ( 'button', 'Open' ) ?: 'Open';
  $padDrawTitle  = padWidgetText ( 'title', $padDrawButton ) ?: $padDrawButton;

  $padContent = padDrawer ( (string) $padParm, $padDrawTitle, $padDrawButton,
                            padWidgetChoice ( 'drawer', 'side', 'right', [ 'right', 'left', 'bottom' ] ), trim ( $padContent ) );

  return TRUE;

?>
