<?php

  if ( $padSeqBuild == 'bool' or $padSeqBuild == 'function' or $padSeqBuild == 'check' ) 
    include_once "/pad/sequence/types/$padSeqSeq/$padSeqBuild.php";

  if ( $padSeqBuild == 'check' and file_exists ( "/pad/sequence/types/$padSeqSeq/bool.php") ) 
    include_once "/pad/sequence/types/$padSeqSeq/bool.php";

?>