<?php

  if     ( file_exists ( "/pad/sequence/types/$padSeqSeq/order.php")    ) $padSeqBuild = 'order';
  elseif ( file_exists ( "/pad/sequence/types/$padSeqSeq/fixed.php")    ) $padSeqBuild = 'fixed';
  elseif ( file_exists ( "/pad/sequence/types/$padSeqSeq/jump.php")     ) $padSeqBuild = 'jump';
  elseif ( file_exists ( "/pad/sequence/types/$padSeqSeq/function.php") ) $padSeqBuild = 'function';
  elseif ( file_exists ( "/pad/sequence/types/$padSeqSeq/loop.php")     ) $padSeqBuild = 'loop';
  elseif ( file_exists ( "/pad/sequence/types/$padSeqSeq/make.php")     ) $padSeqBuild = 'make';
  elseif ( file_exists ( "/pad/sequence/types/$padSeqSeq/bool.php")     ) $padSeqBuild = 'bool';
  else                                                               
    padError ( "Sequence build not found: $padSeqSeq ");

  if ( $padSeqBuild == 'function' ) include_once "/pad/sequence/types/$padSeqSeq/function.php";
  if ( $padSeqBuild == 'bool'     ) include_once "/pad/sequence/types/$padSeqSeq/bool.php";

  if ( $padSeqFixed !== FALSE )
    include '/pad/sequence/build/types/store.php';
  else
    include "/pad/sequence/build/types/$padSeqBuild.php";
 
?>