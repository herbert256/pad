<?php
 
   if ( file_exists ( "sequence/types/$padSeqSeq/check.php" ) ) {

    include_once "sequence/types/$padSeqSeq/check.php";

    if ( file_exists ( "sequence/types/$padSeqSeq/bool.php") ) 
      include_once "sequence/types/$padSeqSeq/bool.php";

    if ( file_exists ( "sequence/types/$padSeqSeq/generated.php") ) 
      include_once "sequence/types/$padSeqSeq/generated.php";
  
  }
  
  $padRet = ( 'padSeqCheck'     . ucfirst ( $padSeqSeq ) ) ( $padSeqStart, $padSeqLoop );
  $padSeq = ( $padRet ) ? 1 : 0;

?>