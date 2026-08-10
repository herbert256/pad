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

  // No } is left, so a { that remains can never open a tag. Strict mode reports it;
  // the lenient walk escapes it as literal text, the mirror of what level/done.php does
  // for a } without an open - it used to stay raw, and a strict level enclosing a
  // lenient pass tripped over its own sandbox's answer.

  $padOpenStart = strrpos ( $padOut [$pad], '{' );

  if ( $padOpenStart !== FALSE ) {

    if ( $padCheckSyntax and substr ( $padOut [$pad], $padOpenStart, 2 ) == '{#' )
      return padError ( "the comment {# never closes" );

    if ( $padCheckSyntax )
      return padError ( "No close } found for open { at position " . $padOpenStart + 1);

    $padOut [$pad] = str_replace ( '{', '&open;', $padOut [$pad] );

  }

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

  if ( $padCheckSyntax )
    include PAD . 'level/unread.php';

  include PAD . 'level/pipes/after.php';

  if ( $padInfo )
    include PAD . 'events/levelEnd.php';

  padResetLvl ($pad);

  $pad--;

  if ( $pad >= 0 )
    padLevel ( $padResult [$pad+1] );

?>