<?php

  include 'sequence/inits/find/inits.php';
  include 'sequence/inits/find/prefix.php';
  include 'sequence/inits/find/tag.php';  
  include 'sequence/inits/find/quick.php';

  include 'sequence/inits/find/pull.php';

      if ( ! $padSeqSeq    and $padSeqFindType == 'play'     ) include 'sequence/inits/find/loop.php';
  elseif ( ! $padSeqSeq    and $padSeqFindType == 'sequence' ) include 'sequence/inits/find/loop.php';
  elseif ( ! $padSeqAction and $padSeqFindType == 'action'   ) include 'sequence/inits/find/loop.php';

  include 'sequence/inits/find/parm.php';

  if     ( ! $padSeqPull and ! $padSeqSeq ) include 'sequence/inits/find/default.php';
  elseif (   $padSeqPull and   $padSeqSeq ) include 'sequence/inits/find/add.php';

?>