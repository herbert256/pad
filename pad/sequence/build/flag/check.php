<?php
 
  if     ( $padSeqBuild == 'fixed' ) $padSeq = padError ( "No sequence found for 'flag' option");
  elseif ( $padSeqBuild == 'store' ) $padSeq = padError ( "No sequence found for 'flag' option");
  elseif ( $padSeqBuild == 'start' ) $padSeq = padError ( "No sequence found for 'flag' option");

  if ( ! file_exists ( "sequence/types/$padSeqSeq/check.php" ) )
    padError ( "No sequence found for 'flag' option");

  include 'sequence/build/flag/go.php';

?>