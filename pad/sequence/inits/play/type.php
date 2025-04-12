<?php

  $padSeqSeq = '';

  if ( padSeqPlay ( $padSeqPrefix ) and file_exists ( "sequence/types/$padSeqTag" ) ) {

    $padSeqSeq = $padSeqTag;

    if ( $padSeqPrefix == 'make' )
      $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqPrefix );
    else 
      $padSeqBuild = $padSeqPrefix;

  } elseif ( padSeqPlay ( $padSeqTag ) and file_exists ( "sequence/types/$padSeqPrefix" ) ) {

    $padSeqSeq = $padSeqPrefix;

    if ( $padSeqTag == 'make' )
      $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqTag );
    else 
      $padSeqBuild = $padSeqTag;

  } 

?>