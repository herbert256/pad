<?php

  $pad_seq_chk = PAD . "sequence/types/$pad_seq_seq";

  if     ( pad_file_exists ( "$pad_seq_chk/order.php")    ) $pad_seq_build = 'order';
  elseif ( pad_file_exists ( "$pad_seq_chk/fixed.php")    ) $pad_seq_build = 'fixed';
  elseif ( pad_file_exists ( "$pad_seq_chk/bool.php")     ) $pad_seq_build = 'bool';
  elseif ( pad_file_exists ( "$pad_seq_chk/function.php") ) $pad_seq_build = 'function';
  elseif ( pad_file_exists ( "$pad_seq_chk/main.php")     ) $pad_seq_build = 'main';
  elseif ( pad_file_exists ( "$pad_seq_chk/loop.php")     ) $pad_seq_build = 'loop';
  elseif ( pad_file_exists ( "$pad_seq_chk/make.php")     ) $pad_seq_build = 'make';
  elseif ( pad_file_exists ( "$pad_seq_chk/filter.php")   ) $pad_seq_build = 'filter';
  
  else pad_error ("No type definition found for '$pad_seq_seq'");

?>