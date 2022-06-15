<?php

  if     ( $pad_seq_random and pad_file_exists (PAD . "sequence/types/$pad_seq_seq/random.php") )          return 'random';
  elseif ( $pad_seq_type == 'from' and pad_file_exists (PAD . "sequence/types/$pad_seq_seq/from_to.php") ) return 'from_to';
  elseif ( $pad_seq_type == 'min'  and pad_file_exists (PAD . "sequence/types/$pad_seq_seq/min_max.php") ) return 'min_max';
  elseif ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/order.php") )                               return 'order';
  elseif ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/fixed.php") )                               return 'fixed';
  elseif ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/jump.php") )                                return 'jump';
  elseif ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/from_to.php") )                             return 'from_to';
  elseif ( pad_file_exists (PAD . "sequence/types/$pad_seq_seq/min_max.php") )                             return 'min_max';
  elseif ( pad_file_exists (PAD . "sequence/functions/bool/$pad_seq_seq.php") )                            return 'bool';
  elseif ( pad_file_exists (PAD . "sequence/functions/Nth/$pad_seq_seq.php") )                             return 'Nth';
  else                                                                                                     return 'loop';

?>