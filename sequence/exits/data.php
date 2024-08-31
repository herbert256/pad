<?php

  if ( ! $padSeqToData ) 
    return;

  if ( $padSeqPull and ( isset ($padSeqPop) or isset ($padSeqShift) ) )
    $padDataStore [$padSeqToData] = padData ( $padSeqStore [$padSeqPull], '', $padSeqToData );
  else
    $padDataStore [$padSeqToData] = padData ( $padSeqResult,              '', $padSeqToData );

?>