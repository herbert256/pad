<?php
  
  foreach ( $padSeqActions as $padSeqAction ) {

    extract ( $padSeqAction );

    $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

  }
  
?>