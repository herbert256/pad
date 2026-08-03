<?php

  set_time_limit ( 300 );

  include APP . 'sequence/clean.php';

  foreach ( glob ( PT . '*', GLOB_ONLYDIR ) as $typeDir ) {

    $type = basename ( $typeDir );

    include APP . 'sequence/flags.php';
    include APP . 'sequence/parm.php';
    include APP . 'sequence/generated.php';
    include APP . 'sequence/basic.php';
    include APP . 'sequence/keepRemoveFlag.php';
    include APP . 'sequence/playSingle.php';
    include APP . 'sequence/playDouble.php';
  }

?>