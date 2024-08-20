<?php

  if ( $padSeqPull and ( isset ($padSeqPop) or isset ($padSeqShift) ) )
    $padData [$padSeqToData] = padData ( $padSeqStore [$padSeqPull], '', $padSeqToData );
  else
    $padData [$padSeqToData] = padData ( $padSeqResult,              '', $padSeqToData );

  padDone ('toData');

?>