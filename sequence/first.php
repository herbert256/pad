<?php

  if ( $pad_seq_random and $pad_tag <> 'random' and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) ) 

    $pad_seq_init = include PAD_HOME . "sequence/types/$pad_tag/random.php"  ;

  elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/init.php" )) 

    $pad_seq_init = include PAD_HOME . "sequence/types/$pad_tag/init.php";

  else

    $pad_seq_init = $pad_seq_min;

  if ( is_null($pad_seq_init) or $pad_seq_init === FALSE )
    return FALSE;
 
  if ( is_array ($pad_seq_init) and ! count($pad_seq_init) )
    return FALSE;
     
  if ( ! is_array($pad_seq_init) ) {
    $pad_seq_save = $pad_seq_init;
    $pad_seq_init = [];
    $pad_seq_init [] = $pad_seq_save;
  }

  return $pad_seq_init

?>