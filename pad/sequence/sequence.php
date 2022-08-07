<?php

  $pIn_sequence = TRUE;

  include 'build/parms.php';
  include 'build/sequence.php';
  include 'build/vars.php';
  include 'build/loop.php';
  include 'build/lists.php';
  include 'build/operations.php';
  include 'build/page.php';
  include 'build/done.php';
  include 'build/build.php';  

  if ( $pSeq_build == 'function' ) include_once "types/$pSeq_seq/function.php";
  if ( $pSeq_build == 'bool'     ) include_once "types/$pSeq_seq/bool.php";

  if ( file_exists ( PAD . "sequence/types/$pSeq_seq/init.php" )) 
    include "types/$pSeq_seq/init.php";
      
  include 'type/type.php';
  include 'build/actions.php';
 
  if ( file_exists ( PAD . "sequence/types/$pSeq_seq/exit.php" ) )   
    include "types/$pSeq_seq/exit.php";

  include 'build/push.php';

  $pIn_sequence = FALSE;

  return $pSeq_result;

?>