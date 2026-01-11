<?php

  global $padInfo;

  $kind = $result [$k] [2];
  $name = $result [$k] [0];

  $padGetName = $name;

  if ( file_exists ( PAD . "eval/single/$kind.php" ) ) {

    if ( $padInfo )
      include PAD . 'events/functionSingle.php';

    $value = include PAD . 'eval/type/single.php';

  }  else {
   
    if ( $padInfo )
      include PAD . 'events/functionParms.php';
  
    $value = include PAD . 'eval/type/parms.php';

  }

  $result [$k] [1] = 'VAL';
  $result [$k] [0] = $value;

  unset ( $result [$k] [2] );
  unset ( $result [$k] [3] );

  padEvalTrace ( 'type9', $result );

  padEvalOpr ( $result, $myself, $start, $end ); padEvalTrace ( 'opr4', $result );

?>