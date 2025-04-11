<?php

  $padSeqSeq = '';

  if ( $padSeqFirst and file_exists ( "sequence/types/$padSeqTag" ) )
  
    $padSeqSeq = $padSeqTag;
      
  elseif ( $padSeqFirst and file_exists ( "sequence/types/$padSeqPrefix" ) )
  
    $padSeqSeq = $padSeqPrefix;
      
  elseif ( $padSeqFirst and file_exists ( "sequence/types/$padSeqFirst" ) ) {
  
    $padSeqSeq = $padSeqFirst;
  
    if ( $padSeqFirstParm and $padSeqFirstParm !== TRUE and ! $padSeqParm )
      $padSeqParm = $padSeqFirstParm;
  
  } elseif ( $padSeqSecond and file_exists ( "sequence/types/$padSeqSecond" ) ) {

    $padSeqSeq = $padSeqSecond;

    if ( $padSeqSecondParm and $padSeqSecondParm !== TRUE and ! $padSeqParm )
      $padSeqParm = $padSeqSecondParm;

  }

  if ( $padSeqSeq ) {
   
    $padSeqPull     = $padSeqPullName;
    $padSeqPullName = '';
   
    if     ( padSeqPlay ( $padSeqType ) ) $padSeqPlay = $padSeqType;
    elseif ( padSeqPlay ( $padSeqTag  ) ) $padSeqPlay = $padSeqTag;
    else                                  $padSeqPlay = 'make';
  
  }

?>