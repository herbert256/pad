<?php

  if ( $padSeqName )
    return;

      if ( $padSeqPush     and $padSeqPush     !== TRUE ) $padSeqName = $padSeqPush;           
  elseif ( $padSeqPull     and $padSeqPull     !== TRUE ) $padSeqName = $padSeqPull;           
  elseif ( $padSeqPullName and $padSeqPullName !== TRUE ) $padSeqName = $padSeqPullName;           
  elseif ( $padSeqSetName                               ) $padSeqName = $padSeqSetName;
  elseif ( file_exists ( "sequence/types/$padSeqSeq" )  ) $padSeqName = $padSeqSeq;
  else                                                    $padSeqName = 'sequence'; 
  
  $padName [$pad] = $padSeqName;

?>