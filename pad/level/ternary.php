<?php

  // Implements the {tag ? true : false} option form. The option text after the '?' is cut
  // at the first unquoted colon and only the matching side is evaluated - the true side
  // onto $padContent, the false side onto $padFalse - which level/base.php then picks
  // between.

  list ( $padTernaryTrue, $padTernaryFalse ) =
    padSplitOnUnquotedColon ( substr ( $padOpt [$pad] [0], 1 ) );

  if ( $padTagResult )
    $padContent .= padEval ( $padTernaryTrue );
  else
    $padFalse .= padEval ( $padTernaryFalse );

?>