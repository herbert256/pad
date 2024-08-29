<?php
  
  $padSeqStoreList = [];

  foreach ( $padOptionsSingle as $padSeqK => $padSeqV ) 
   if ( file_exists ( "/pad/sequence/store/types/$padSeqK.php" ) ) 
     $padSeqStoreList [$padSeqK] = $padSeqV  ;
  
?>