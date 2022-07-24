<?php

  $pad_in_sequence = TRUE;

  include 'build/parms.php';
  include 'build/sequence.php';
  include 'build/vars.php';
  include 'build/loop.php';
  include 'build/lists.php';
  include 'build/operations.php';
  include 'build/page.php';
  include 'build/done.php';
  include 'build/build.php';  

  if ( $pad_seq_build == 'function' ) include_once "types/$pad_seq_seq/function.php";
  if ( $pad_seq_build == 'bool'     ) include_once "types/$pad_seq_seq/bool.php";

  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/init.php" )) 
    include "types/$pad_seq_seq/init.php";
      
  include 'type/type.php';
  include 'build/actions.php';
 
  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/exit.php" ) )   
    include "types/$pad_seq_seq/exit.php";

  include 'build/push.php';

  $pad_in_sequence = FALSE;

  return $pad_seq_result;

?>