<?php
  
  $padSeqStoreList = [];

  foreach ( $padOptionsSingle as $padStartOption ) 
   if ( file_exists ( "/pad/sequence/store/types/$padPrmName.php" ) ) 
     $padSeqStoreList [$padPrmName] = $padPrmValue;
  
?>