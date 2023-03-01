<?php

  # global $padLogShort , $padLogPnt, $padOccur, $padData, $padCurrent, $pad;

  if ( $padLogShort and padIsDefaultData ( $padData[$pad] ) )
    return;

  $padLogNow [ "Data" ] = $padCurrent[$pad];

?>