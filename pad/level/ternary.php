<?php

  // Implements the {tag ? true : false} option form. The option text after the '?' is cut
  // at the first unquoted colon and only the matching side answers - the true side onto
  // $padContent, the false side onto $padFalse - which level/base.php then picks between.
  //
  // A side that is one bare word is that word: {even@rows ? even : odd} answers 'even' or
  // 'odd', the way every manual example reads. Evaluating it, as this used to, read the
  // word as a property of this transient one-occurrence level instead - 'odd' answered 1
  // whatever row the target stood on, and 'even' answered nothing, which the audit caught
  // recorded in the suite as if it were the answer. A $field, a quoted string or a longer
  // expression is still evaluated.

  list ( $padTernaryTrue, $padTernaryFalse ) =
    padSplitOnUnquotedColon ( substr ( $padOpt [$pad] [0], 1 ) );

  $padTernaryGo = trim ( ( $padTagResult ) ? $padTernaryTrue : $padTernaryFalse );

  if ( ! preg_match ( '/^[A-Za-z_][A-Za-z0-9_-]*$/', $padTernaryGo ) )
    $padTernaryGo = padEval ( $padTernaryGo );

  if ( $padTagResult )
    $padContent .= $padTernaryGo;
  else
    $padFalse .= $padTernaryGo;

?>