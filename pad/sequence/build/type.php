<?php

  if     ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/order.php")    ) return 'order';
  elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/fixed.php")    ) return 'fixed';
  elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/bool.php")     ) return 'bool';
  elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/function.php") ) return 'function';
  elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/loop.php")     ) return 'loop';
  elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/make.php")     ) return 'make';
  elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/filter.php")   ) return 'filter';

  pad_error ("No type definition found for '$pad_seq_seq'");

?>