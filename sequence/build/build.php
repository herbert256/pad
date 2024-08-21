<?php
  
  include "/pad/sequence/build/include.php";

  if ( $padSeqFixed !== FALSE )
    include '/pad/sequence/build/types/store.php';
  else
    include "/pad/sequence/build/types/$padSeqBuild.php";
 
?>