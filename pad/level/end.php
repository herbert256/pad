<?php

  // Reached when no '}' is left in $padOut [$pad]: the current occurrence has rendered, so
  // either advance the level or close it down.
  //
  // The occurrence is banked into $padResult [$pad] (occurrence/end.php) and, while rows
  // remain in $padData [$pad] - or a {walk} asks for another pass, or a deferred @start@ /
  // @end@ section is still pending - control goes back to occurrence/occurrence.php.
  // Otherwise the walk end pass, the exit callback, the end-of-level options and the
  // closing pipe run, padResetLvl() restores the globals this level shadowed, $pad drops by
  // one and padLevel() splices $padResult back into the parent level's text.

  if ( isset ( $padOccurStart [$pad] ) )
    if ( isset ( $padOccurStart [$pad] [$padOccur[$pad]] ) )
      include PAD . 'occurrence/end.php';

  if ( next ($padData [$pad]) !== FALSE )
    return include PAD . 'occurrence/occurrence.php';

  if ( $padWalk [$pad] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $padWalk [$pad] == 'next' )
      return include PAD . 'occurrence/occurrence.php';
  }

  if ( $padStartBase [$pad] ) return include PAD . 'level/start_end/start2.php';
  if ( $padEndBase   [$pad] ) return include PAD . 'level/start_end/end2.php';

  $padOccur [$pad] = 99999;

  if ( $padWalk [$pad] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include PAD . 'callback/exit.php' ;

  include PAD . 'options/_go/end.php';
  include PAD . 'level/pipes/after.php';

  if ( $padInfo )
    include PAD . 'events/levelEnd.php';

  padResetLvl ($pad);

  $pad--;

  if ( $pad >= 0 )
    padLevel ( $padResult [$pad+1] );

?>