<?php

  $pSeq_chk = PAD . "sequence/types/$pSeq_seq";

  if     ( file_exists ( "$pSeq_chk/order.php")    ) $pSeq_build = 'order';
  elseif ( file_exists ( "$pSeq_chk/fixed.php")    ) $pSeq_build = 'fixed';
  elseif ( file_exists ( "$pSeq_chk/bool.php")     ) $pSeq_build = 'bool';
  elseif ( file_exists ( "$pSeq_chk/function.php") ) $pSeq_build = 'function';
  elseif ( file_exists ( "$pSeq_chk/main.php")     ) $pSeq_build = 'main';
  elseif ( file_exists ( "$pSeq_chk/loop.php")     ) $pSeq_build = 'loop';
  elseif ( file_exists ( "$pSeq_chk/make.php")     ) $pSeq_build = 'make';
  elseif ( file_exists ( "$pSeq_chk/filter.php")   ) $pSeq_build = 'filter';
  
  else pError ("No type definition found for '$pSeq_seq'");

?>