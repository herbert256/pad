<?php

  include pad . 'tail/types/trace/store/start.php';

  if ( $padOpt [$pad] [1] )
    $padTraceProfile = $padOpt [$pad] [1];

  $padTraceType = 'tag'; 
  $padTraceGo   = $pad;

  include pad . 'tail/types/trace/trace/start.php';

  $padTraceLevel [$pad] = "*SKIP*";;
  $padTraceSkipLevel    = $pad;

?>