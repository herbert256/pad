<?php

  include 'build/parms.php';
  include 'build/sequence.php';
  include 'build/vars.php';
  include 'build/loop.php';
  include 'build/lists.php';
  include 'build/operations.php';
  include 'build/page.php';
  include 'build/done.php';
  include 'build/build.php';  

  if ( $padSeqBuild == 'function' ) include_once "types/$padSeqSeq/function.php";
  if ( $padSeqBuild == 'bool'     ) include_once "types/$padSeqSeq/bool.php";

  if ( file_exists ( PAD . "pad/sequence/types/$padSeqSeq/init.php" )) 
    include "types/$padSeqSeq/init.php";
      
  include 'type/type.php';
  include 'build/actions.php';
 
  if ( file_exists ( PAD . "pad/sequence/types/$padSeqSeq/exit.php" ) )   
    include "types/$padSeqSeq/exit.php";

  include 'build/push.php';
  include 'build/return.php';
  include 'build/trace.php';

  return $padSeqReturn;

?>