<?php

  if ( ! isset ( $padLogPnt [$pad] ['occurrences'] [$padOccur[$pad]] ) )
    $padLogPnt [$pad] ['occurrences'] [$padOccur[$pad]] = [];

  $padLogNow = &$padLogPnt [$pad] ['occurrences'] [$padOccur[$pad]];

  if ( $padLogData ) 
    include 'data.php';

?>