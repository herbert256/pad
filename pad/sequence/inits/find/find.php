<?php

  include 'sequence/inits/find/inits.php';
  include 'sequence/inits/find/prefix.php';
  include 'sequence/inits/find/tag.php';  
  include 'sequence/inits/find/quick.php';
  include 'sequence/inits/find/fastParm.php';
  include 'sequence/inits/find/loop.php';
  include 'sequence/inits/find/pull.php';
  include 'sequence/inits/find/parm.php';

  if     ( ! $pqPull and ! $pqSeq ) include 'sequence/inits/find/default.php';
  elseif (   $pqPull and   $pqSeq ) include 'sequence/inits/find/add.php';

?>