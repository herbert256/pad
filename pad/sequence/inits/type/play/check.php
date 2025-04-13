<?php

  if ( $padSeqTag == 'sequence' and $padSeqPrefix  and file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) ) 
    $padSeqAction = $padSeqPrefix;
  elseif ( $padSeqPrefix == 'sequence' and $padSeqTag and  file_exists ( "sequence/actions/types/$padSeqTag.php"    ) ) 
    $padSeqAction = $padSeqTag;
  else 
    return;

  $padSeqActionAfterName = $padSeqAction;
  $padSeqActionAfterParm = $padSeqParm;
   
?>