<?php

  if ( in_array ( $padSeqType, ['pull','sequence'] ) )
    if ( isset ( $padSeqStore [$padSeqTag] ) )
      $padSeqPull = $padSeqTag;

  if ( in_array ( $padSeqTag, ['pull','sequence'] ) )
    if ( isset ( $padSeqStore [$padSeqPrefix] ) ) 
      $padSeqPull = $padSeqPrefix;
    
  if ( $padSeqPull )
    include 'sequence/inits/go/store.php';

?>