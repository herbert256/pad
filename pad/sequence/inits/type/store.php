<?php

{mySequence make,    add,3  } {$mySequence} {/mySequence}

  $padSeqPull = '';

  if ( in_array ( $padSeqType, ['store','sequence'] ) )
    if ( isset ( $padSeqStore [$padSeqTag] ) )
      $padSeqPull  = $padSeqTag;

  if ( in_array ( $padSeqTag, ['store','sequence'] ) )
    if ( isset ( $padSeqStore [$padSeqPrefix] ) ) 
      $padSeqPull = $padSeqPrefix;
    
  if ( $padSeqPull )
    include 'sequence/inits/go/store.php';

?>