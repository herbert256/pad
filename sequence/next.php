<?php

  if ( isset ( $pad_seq_init [$pad_seq_idx-1] ) ) 
    return $pad_seq_init [$pad_seq_idx-1];

  if ( $pad_seq_random and $pad_tag <> 'random' and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) )
    return include PAD_HOME . "sequence/types/$pad_tag/random.php" ;
 
  if ( ! pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/row.php" ))
    return FALSE;

  $G = &$pad_seq_base;
  $n = $pad_seq_idx-1;

  return include PAD_HOME . "sequence/types/$pad_tag/row.php";

?>