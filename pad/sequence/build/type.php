<?php

  if     ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/order.php") )    return 'order';
  elseif ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/fixed.php") )    return 'fixed';
  elseif ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/bool.php") )     return 'bool';
  elseif ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/function.php") ) return 'function';
  else                                                                          return 'loop';

?>