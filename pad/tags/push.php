<?php

  // {push 'scripts', once='chart'} ... {/push}: renders its content where it stands, prints
  // nothing there, and adds the rendering to the stack of that name, for a {stack 'scripts'}
  // anywhere in the page - lib/stack.php. Like {tidy} it runs twice: first to ask for the
  // end walk, then with the rendered content.
  //
  // With a once= key a later push of the same key is skipped before its content renders;
  // once without a key drops a push whose rendered text the stack already holds.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {push} never closes" );

  if ( trim ( (string) $padParm ) === '' ) {
    if ( $padCheckSyntax )
      padError ( "the {push} needs the name of a stack - {push 'scripts'}" );
    return NULL;
  }

  $padPushOnce = padTagParm ( 'once', '' );

  if ( $padWalk [$pad] == 'start' ) {

    if ( padStackSeen ( $padParm, $padPushOnce ) )
      return NULL;

    $padWalk [$pad] = 'end';
    return TRUE;

  }

  padStackPush ( $padParm, $padContent, $padPushOnce );

  $padContent = '';

  return TRUE;

?>
