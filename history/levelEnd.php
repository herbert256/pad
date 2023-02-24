<?php

  #if ( $pad )
    $padHistoryResult [ $padHistoryLevel [$pad] ] [ 'result' ] = $padResult [$pad];
  #else
  #  $padHistoryResult [ 0 ]                       [ 'result' ] = $padResult [$pad];

?>