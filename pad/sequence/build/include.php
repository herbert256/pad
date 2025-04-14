<?php

   if ( file_exists ( "sequence/types/$padSeqSeq/bool.php") ) 
     include_once "sequence/types/$padSeqSeq/bool.php";
 
   if ( file_exists ( "sequence/types/$padSeqSeq/function.php") ) 
     include_once "sequence/types/$padSeqSeq/function.php";

  if ( $padSeqBuild == 'bool' or $padSeqBuild == 'function' or $padSeqBuild == 'check' ) 
    include_once "sequence/types/$padSeqSeq/$padSeqBuild.php";

  if ( in_array ( $padSeqBuild, ['check','keep','remove'] )
    and file_exists ( "sequence/types/$padSeqSeq/bool.php") ) 
    include_once "sequence/types/$padSeqSeq/bool.php";

  if ( in_array ( $padSeqBuild, ['check','keep','remove'] ) 
    and ! file_exists ( "sequence/types/$padSeqSeq/bool.php" ) 
    and   file_exists ( "sequence/types/$padSeqSeq/generated.php") ) 
    include_once "sequence/types/$padSeqSeq/generated.php";

?>