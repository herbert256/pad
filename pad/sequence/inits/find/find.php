<?php

  $padSeqFindParm = $padOpt [$pad] [1];

  include 'sequence/inits/find/type.php';
  include 'sequence/inits/find/prefix.php';
  include 'sequence/inits/find/tag.php';  
  include 'sequence/inits/find/integer.php';
  include 'sequence/inits/find/quick.php';

  $padSeqPull = include 'sequence/inits/find/pull.php';

  if ( ! $padSeqSeq    and $padSeqFindType == 'play'     ) include 'sequence/inits/find/sequence.php';
  if ( ! $padSeqSeq    and $padSeqFindType == 'sequence' ) include 'sequence/inits/find/sequence.php';
  if ( ! $padSeqAction and $padSeqFindType == 'action'   ) include 'sequence/inits/find/action.php';

  include 'sequence/inits/find/parm.php';

  if     ( ! $padSeqPull and ! $padSeqSeq ) include 'sequence/inits/find/default.php';
  elseif (   $padSeqPull and   $padSeqSeq ) include 'sequence/inits/find/add.php';

?>