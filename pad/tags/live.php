<?php

  // {live 'cart'} ... {/live}: a region that re-renders on the server when something in it
  // is clicked, submitted or changed, and swaps itself in - lib/live.php. Like {spaceless}
  // it runs twice: first to ask for the end walk, then on the rendered content, which it
  // wraps in the region and keeps for a live request.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {live} never closes" );

    padLiveName ( $padParm );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padContent = padLiveWrap ( padLiveName ( $padParm ), $padContent );

  return TRUE;

?>
