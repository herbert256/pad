<?php

  include 'sequence/inits/direct.php';
  include 'sequence/inits/clear.php';
  include 'sequence/inits/vars.php';
  include 'sequence/actions/set.php';
  include 'sequence/plays/inits.php';
  include 'sequence/actions/inits.php';
  include 'sequence/inits/limits.php';
  include 'sequence/build/build.php';
  include 'sequence/exits/actions.php';
  include 'sequence/exits/done.php';
  include 'sequence/exits/info.php';

  return array_values ( $pqResult );

?>