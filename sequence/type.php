<?php

  if ( $pad_seq_pull ) 
    $pad_seq_build = 'pull';  
  elseif ( $pad_seq_random and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) ) 
    $pad_seq_build = 'random';
  elseif ( $pad_seq_type == 'from' and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/from_to.php") )
    $pad_seq_build = 'from_to';
  elseif ( $pad_seq_type == 'min' and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/min_max.php") )
    $pad_seq_build = 'min_max';
  elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/order.php" ) ) 
    $pad_seq_build = 'order';
  elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/fixed.php" ) ) 
    $pad_seq_build = 'fixed';
  elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/jump.php" ) ) 
    $pad_seq_build = 'jump';
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/from_to.php") )
    $pad_seq_build = 'from_to';
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/min_max.php") )
    $pad_seq_build = 'min_max';
  else                                                                                               
    $pad_seq_build = 'loop';

  include PAD_HOME . "sequence/build/$pad_seq_build.php";
  
?>