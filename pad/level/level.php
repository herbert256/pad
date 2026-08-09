<?php

  // One turn of the tag-processing loop: locate the innermost { } pair in $padOut [$pad]
  // and dispatch it. Included over and over by start/pad/level.php until the level is done.
  //
  // padLevelEnd() takes the first '}' and padLevelStart() the '{' nearest before it, so the
  // innermost tag is always handled first; $padStart/$padEnd [$pad] delimit it and
  // $padBetween holds its text. No '}' left means the level has been fully rendered
  // (level/end.php); no '{' before it means a stray brace. Comments {# .. #}, orphan
  // closing tags, blank tags and the $ ! # & ? variable forms are settled here; the rest
  // goes on to level/type.php, level/tag.php and level/start.php, which opens a new level.

  if ( $padRestart )
    return include PAD . 'start/restart.php';

  if ( padLevelEnd   () === FALSE ) return include PAD . 'level/end.php';
  if ( padLevelStart () === FALSE ) return include PAD . 'level/done.php';

  padLevelBetween ();

  if ( padWhiteCheck   () ) return padWhiteHit   ();
  if ( padCloseCheck   () ) return padCloseHit   ();
  if ( padCommentCheck () ) return padCommentHit ();

  include PAD . 'level/pipes/start.php';
  include PAD . 'level/between.php';

  if ( $pad and $padLvlFun [$pad-1] )
    include PAD . 'level/function.php';

  if ( in_array ( $padFirst, ['$','!','#','&','?'] ) ) {
    $padTry = 'level/var';
    return include PAD . 'try/try.php';
  }

  include PAD . 'level/type.php';
  include PAD . 'level/tag.php';

  if ( ! $padTypeResult )
    return include PAD . 'level/no.php';

  return include PAD . 'level/start.php';

?>