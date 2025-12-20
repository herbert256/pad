<?php

  global $pqStore;

  include PQ . 'inits/direct.php';
  include PQ . 'inits/clear.php';
  include PQ . 'inits/vars.php';
  include PQ . 'exits/info.php';

  return $pqStore [$name];

?>
