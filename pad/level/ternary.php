<?php

  list ( $padTernaryTrue, $padTernaryFalse ) =
    padSplitOnUnquotedColon ( substr ( $padOpt [$pad] [0], 1 ) );

  if ( $padTagResult )
    $padContent .= padEval ( $padTernaryTrue );
  else
    $padFalse .= padEval ( $padTernaryFalse );

?>
