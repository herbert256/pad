<?php

  include_once 'sequence/lib/sequence.php';

  include 'sequence/build/parms.php';
  include 'sequence/build/sequence.php';
  include 'sequence/build/vars.php';
  include 'sequence/build/loop.php';
  include 'sequence/build/lists.php';
  include 'sequence/build/operations.php';
  include 'sequence/build/page.php';
  include 'sequence/build/done.php';
  include 'sequence/build/build.php';  

  if ( $padSeqBuild == 'function' ) include_once "sequence/types/$padSeqSeq/function.php";
  if ( $padSeqBuild == 'bool'     ) include_once "sequence/types/$padSeqSeq/bool.php";

  if ( padExists ( pad . "sequence/types/$padSeqSeq/init.php" )) 
    include "sequence/types/$padSeqSeq/init.php";
      
  include 'sequence/type/type.php';
  include 'sequence/build/actions.php';
 
  if ( padExists ( pad . "sequence/types/$padSeqSeq/exit.php" ) )   
    include "sequence/types/$padSeqSeq/exit.php";

  include 'sequence/build/push.php';
  include 'sequence/build/return.php';

  return $padSeqReturn;

?>