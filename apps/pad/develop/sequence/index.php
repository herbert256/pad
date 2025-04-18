<?php

  set_time_limit ( 300 );

  include APP . 'develop/sequence/clean.php';
  include APP . 'develop/sequence/actions.php';

  foreach ( pqTypes () as $type ) {
    include APP . 'develop/sequence/flags.php';
    include APP . 'develop/sequence/parm.php';
    include APP . 'develop/sequence/generated.php';
    include APP . 'develop/sequence/check.php';
    include APP . 'develop/sequence/basic.php';
    include APP . 'develop/sequence/type.php';
    include APP . 'develop/sequence/keepRemoveFlag.php';
    include APP . 'develop/sequence/playSingle.php';
    include APP . 'develop/sequence/playDouble.php';
  }  

  file_put_contents ( "sequence/types/range/flags/parm", 1 );
  file_put_contents ( "sequence/types/list/flags/parm",  1 );

?>