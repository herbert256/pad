<?php

  if ( ! isset ( $padSeqStore ) )
    $padSeqStore = [];

  $padSeqOpr   = ['make', 'keep', 'remove'];
  $padSeqTypes = pad . 'sequence/types';

  include_once pad . 'sequence/inits/lib.php';
  
  include pad . 'sequence/inits/parms.php';
  include pad . 'sequence/inits/sequence.php';
  include pad . 'sequence/inits/vars.php';
  include pad . 'sequence/inits/loop.php';
  include pad . 'sequence/inits/operations.php';
  include pad . 'sequence/inits/done.php';
  include pad . 'sequence/inits/type.php';  

  if ( $padSeqBuild == 'function' ) include_once "$padSeqType/function.php";
  if ( $padSeqBuild == 'bool'     ) include_once "$padSeqType/bool.php";

?>