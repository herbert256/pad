<?php
 
  include APP . 'develop/seq/clean.php';
  include APP . 'develop/seq/actions.php';
  include APP . 'develop/seq/one.php';

  foreach ( padSeqTypes () as $type ) {
    include APP . 'develop/seq/flags.php';
    include APP . 'develop/seq/parm.php';
    include APP . 'develop/seq/generated.php';
    include APP . 'develop/seq/check.php';
    include APP . 'develop/seq/basic.php';
    include APP . 'develop/seq/keepRemove.php';
    include APP . 'develop/seq/oprSingle.php';
    include APP . 'develop/seq/oprDouble.php';
    include APP . 'develop/seq/store.php';
  }  

?>