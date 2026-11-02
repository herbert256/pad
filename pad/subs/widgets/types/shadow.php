<?php

  // {widget:shadow css='www:card.css'} ... {/widget:shadow} - the shadow root of a custom element, rendered
  // on the server as <template shadowrootmode="open"> with the stylesheets inlined
  // (subs/widgets/_lib/shadow.php). mode='closed' for a closed root, focus for delegatesFocus.
  //
  // Runs twice, like {pad:tidy}: the content renders first, then the tags go round it.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {widget:shadow} never closes" );

  // While the content renders, a style written once a page is written once in this root:
  // the page's does not reach into it (padWidgetOnce). A toy in the root after a toy outside
  // it had no toy.css there - a canvas without its frame, controls without their look.

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    $padShadowScope [] = 'shadow' . ( $padShadowCount = ( $padShadowCount ?? 0 ) + 1 );
    return TRUE;
  }

  array_pop ( $padShadowScope );

  $padContent = padProtect ( padShadowOpen ( padTagParm ( 'css', '' ), padTagParm ( 'mode', 'open' ), padTagParm ( 'focus', FALSE ) ) )
              . $padContent
              . padProtect ( '</template>' );

  return TRUE;

?>
