<?php

  $padSeq_chk = PAD . "sequence/types/$padSeq_seq";

  if     ( file_exists ( "$padSeq_chk/order.php")    ) $padSeq_build = 'order';
  elseif ( file_exists ( "$padSeq_chk/fixed.php")    ) $padSeq_build = 'fixed';
  elseif ( file_exists ( "$padSeq_chk/bool.php")     ) $padSeq_build = 'bool';
  elseif ( file_exists ( "$padSeq_chk/function.php") ) $padSeq_build = 'function';
  elseif ( file_exists ( "$padSeq_chk/main.php")     ) $padSeq_build = 'main';
  elseif ( file_exists ( "$padSeq_chk/loop.php")     ) $padSeq_build = 'loop';
  elseif ( file_exists ( "$padSeq_chk/make.php")     ) $padSeq_build = 'make';
  elseif ( file_exists ( "$padSeq_chk/filter.php")   ) $padSeq_build = 'filter';
  
  else padError ("No type definition found for '$padSeq_seq'");

?>