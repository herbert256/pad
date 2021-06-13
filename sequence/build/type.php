<?php

  if ( ! $pad_seq_build )
    if ( $pad_seq_loop )
      $pad_seq_build = 'loop';
    elseif ( $pad_seq_pull ) 
      $pad_seq_build = 'pull';  
    elseif ( $pad_seq_random and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) ) 
      $pad_seq_build = 'random';
    elseif ( ($pad_seq_from or $pad_seq_to <> PHP_INT_MAX ) and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/from_to.php") )
      $pad_seq_build = 'loop';
    elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/fixed.php" ) ) 
      $pad_seq_build = 'fixed';
    elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/jump.php" ) ) 
      $pad_seq_build = 'jump';
    else                                                                                               
      $pad_seq_build = 'loop';

  include PAD_HOME . "sequence/build/types/$pad_seq_build.php";
  
?>