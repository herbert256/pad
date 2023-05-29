<?php

  include_once pad . 'sequence/lib/sequence.php';

  include pad . 'sequence/build/parms.php';
  include pad . 'sequence/build/sequence.php';
  include pad . 'sequence/build/vars.php';
  include pad . 'sequence/build/loop.php';
  include pad . 'sequence/build/lists.php';
  include pad . 'sequence/build/operations.php';
  include pad . 'sequence/build/page.php';
  include pad . 'sequence/build/done.php';
  include pad . 'sequence/build/build.php';  

  if ( $padSeqBuild == 'function' ) include_once pad . "sequence/types/$padSeqSeq/function.php";
  if ( $padSeqBuild == 'bool'     ) include_once pad . "sequence/types/$padSeqSeq/bool.php";

  if ( padExists ( pad . "sequence/types/$padSeqSeq/init.php" )) 
    include pad . "sequence/types/$padSeqSeq/init.php";
      
  include pad . 'sequence/type/type.php';
  include pad . 'sequence/build/actions.php';
 
  if ( padExists ( pad . "sequence/types/$padSeqSeq/exit.php" ) )   
    include pad . "sequence/types/$padSeqSeq/exit.php";

  include pad . 'sequence/build/push.php';
  include pad . 'sequence/build/return.php';

  return $padSeqReturn;

?>