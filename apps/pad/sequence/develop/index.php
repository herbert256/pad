<?php

  set_time_limit ( 300 );

  include APP . 'sequence/develop/clean.php';

  foreach ( pqTypes () as $type ) {
    include APP . 'sequence/develop/flags.php';
    include APP . 'sequence/develop/parm.php';
    include APP . 'sequence/develop/generated.php';
    include APP . 'sequence/develop/basic.php';
    include APP . 'sequence/develop/keepRemoveFlag.php';
    include APP . 'sequence/develop/playSingle.php';
    include APP . 'sequence/develop/playDouble.php';
  }  

?>