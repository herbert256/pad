<?php

  $padIn_sequence = TRUE;

  include 'build/parms.php';
  include 'build/sequence.php';
  include 'build/vars.php';
  include 'build/loop.php';
  include 'build/lists.php';
  include 'build/operations.php';
  include 'build/page.php';
  include 'build/done.php';
  include 'build/build.php';  

  if ( $padSeq_build == 'function' ) include_once "types/$padSeq_seq/function.php";
  if ( $padSeq_build == 'bool'     ) include_once "types/$padSeq_seq/bool.php";

  if ( file_exists ( PAD . "sequence/types/$padSeq_seq/init.php" )) 
    include "types/$padSeq_seq/init.php";
      
  include 'type/type.php';
  include 'build/actions.php';
 
  if ( file_exists ( PAD . "sequence/types/$padSeq_seq/exit.php" ) )   
    include "types/$padSeq_seq/exit.php";

  include 'build/push.php';
  include 'build/return.php';
  include 'build/trace.php';

  $padIn_sequence = FALSE;

  return $padSeqReturn;

?>