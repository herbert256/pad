<?php

  if ( isset ( $pad_seq_init [count($pad_seq_result)] ) ) 
    return $pad_seq_init [count($pad_seq_result)];
 
  if ( ! pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/jump.php" ))
    return FALSE;

  $G = &$pad_seq_base;
  $n = count($pad_seq_base) - 1;

  return include PAD_HOME . "sequence/types/$pad_tag/jump.php";

?>