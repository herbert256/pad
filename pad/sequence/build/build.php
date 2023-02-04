<?php

  $padSeqChk = PAD . "sequence/types/$padSeqSeq";

  if     ( file_exists ( "$padSeqChk/order.php")    ) $padSeqBuild = 'order';
  elseif ( file_exists ( "$padSeqChk/fixed.php")    ) $padSeqBuild = 'fixed';
  elseif ( file_exists ( "$padSeqChk/bool.php")     ) $padSeqBuild = 'bool';
  elseif ( file_exists ( "$padSeqChk/function.php") ) $padSeqBuild = 'function';
  elseif ( file_exists ( "$padSeqChk/main.php")     ) $padSeqBuild = 'main';
  elseif ( file_exists ( "$padSeqChk/loop.php")     ) $padSeqBuild = 'loop';
  elseif ( file_exists ( "$padSeqChk/make.php")     ) $padSeqBuild = 'make';
  elseif ( file_exists ( "$padSeqChk/filter.php")   ) $padSeqBuild = 'filter';
  
  else padError ("No type definition found for '$padSeqSeq'");

?>