<?php

  include pad . 'sequence/build/inits.php';
  include pad . 'sequence/build/parms.php';
  include pad . 'sequence/build/sequence.php';
  include pad . 'sequence/build/vars.php';
  include pad . 'sequence/build/loop.php';
  include pad . 'sequence/build/mkr.php';
  include pad . 'sequence/build/operations.php';
  include pad . 'sequence/build/page.php';
  include pad . 'sequence/build/done.php';
  include pad . 'sequence/build/build.php';  

  if ( $padSeqBuild == 'function' ) include_once "$padSeqType/function.php";
  if ( $padSeqBuild == 'bool'     ) include_once "$padSeqType/bool.php";

  if ( padExists ( "$padSeqType/init.php" )) 
    include "$padSeqType/init.php";
      
  include pad . 'sequence/type/type.php';
  include pad . 'sequence/build/actions.php';
 
  if ( padExists ( "$padSeqType/exit.php" ) )   
    include "$padSeqType/exit.php";

  include pad . 'sequence/build/push.php';
  include pad . 'sequence/build/return.php';

  return $padSeqReturn;

?>