<?php

  if ( $padSeqBuild == 'bool' or $padSeqBuild == 'function' or $padSeqBuild == 'check' ) 
    include_once PAD . "seq/types/$padSeqSeq/$padSeqBuild.php";

  if ( in_array ( $padSeqBuild, ['check','keep','remove'] )
    and file_exists ( PAD . "seq/types/$padSeqSeq/bool.php") ) 
    include_once PAD . "seq/types/$padSeqSeq/bool.php";

  if ( in_array ( $padSeqBuild, ['check','keep','remove'] ) 
    and ! file_exists ( PAD . "seq/types/$padSeqSeq/bool.php" ) 
    and   file_exists ( PAD . "seq/types/$padSeqSeq/generated.php") ) 
    include_once PAD . "seq/types/$padSeqSeq/generated.php";

?>