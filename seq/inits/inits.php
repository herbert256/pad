<?php

  if ( ! isset ( $padSeqStore ) )
    $padSeqStore = [];

  $padSeqOpr = ['make', 'keep', 'remove'];

  include_once '/pad/seq/inits/_lib.php';
  
  include '/pad/seq/inits/parms.php';
  include '/pad/seq/inits/seq.php';
  include '/pad/seq/inits/vars.php';
  include '/pad/seq/inits/loop.php';
  include '/pad/seq/inits/operations.php';
  include '/pad/seq/inits/done.php';
  include '/pad/seq/inits/type.php';  

  if ( $padSeqBuild == 'function' ) include_once "$padSeqType/function.php";
  if ( $padSeqBuild == 'bool'     ) include_once "$padSeqType/bool.php";

  if ( file_exists ( "$padSeqType/init.php" )) 
    include "$padSeqType/init.php";

  $padSeqSetName = 'padSeq' . ucfirst($padSeqSeq); 
  if ( ! isset ( $GLOBALS [$padSeqSetName] ) )
    $$padSeqSetName = $padSeqParm;

?>