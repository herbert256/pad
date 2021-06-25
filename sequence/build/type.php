<?php

  if     ( $pad_seq_random and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/random.php") )          return 'random';
  elseif ( $pad_seq_type == 'from' and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/from_to.php") ) return 'from_to';
  elseif ( $pad_seq_type == 'min'  and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/min_max.php") ) return 'min_max';
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/order.php") )                               return 'order';
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/fixed.php") )                               return 'fixed';
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/jump.php") )                                return 'jump';
  elseif ( pad_file_exists (PAD_HOME . "sequence/functions/$pad_tag.php") )                                 return 'function';
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/from_to.php") )                             return 'from_to';
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/min_max.php") )                             return 'min_max';
  else                                                                                                      return 'loop';

?>