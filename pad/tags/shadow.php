<?php

  // {shadow css='www:card.css'} ... {/shadow} - the shadow root of a custom element, rendered
  // on the server as <template shadowrootmode="open"> with the stylesheets inlined
  // (lib/shadow.php). mode='closed' for a closed root, focus for delegatesFocus.
  //
  // Runs twice, like {tidy}: the content renders first, then the tags go round it.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {shadow} never closes" );

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padContent = padProtect ( padShadowOpen ( padTagParm ( 'css', '' ), padTagParm ( 'mode', 'open' ), padTagParm ( 'focus', FALSE ) ) )
              . $padContent
              . padProtect ( '</template>' );

  return TRUE;

?>
