<?php

  include PQ . 'inits/find/parm/inits.php';
  include PQ . 'inits/find/prefix.php';
  include PQ . 'inits/find/tag.php';  
  include PQ . 'inits/find/parm/quick.php';
  include PQ . 'inits/find/parm/primary.php';
  include PQ . 'inits/find/loop.php';
  include PQ . 'inits/find/pull.php';
  include PQ . 'inits/find/parm/parm.php';

  if     ( ! $pqPull and ! $pqSeq ) include PQ . 'inits/find/default.php';
  elseif (   $pqPull and   $pqSeq ) include PQ . 'inits/find/add.php';

?>