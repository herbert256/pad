<?php

  if ( $padSeqBuild == 'bool' or $padSeqBuild == 'function' or $padSeqBuild == 'check' ) 
    include_once "seq/types/$padSeqSeq/$padSeqBuild.php";

  if ( in_array ( $padSeqBuild, ['check','keep','remove'] )
    and file_exists ( "seq/types/$padSeqSeq/bool.php") ) 
    include_once "seq/types/$padSeqSeq/bool.php";

  if ( in_array ( $padSeqBuild, ['check','keep','remove'] ) 
    and ! file_exists ( "seq/types/$padSeqSeq/bool.php" ) 
    and   file_exists ( "seq/types/$padSeqSeq/generated.php") ) 
    include_once "seq/types/$padSeqSeq/generated.php";

?>