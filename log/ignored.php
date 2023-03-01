<?php

  global $padLogShort, $padLogNow;

  if ( $padLogShort )
    $padLogNow [ "Ignored: $info-$padIgnCnt" ]  = $padBetween;
  else
    $padLogNow ['ignored'] ["$info-$padIgnCnt"] = $padBetween ;

?>