<?php

  $kind = $result [$k] [2];
  $name = $result [$k] [0];

  if ( $GLOBALS ['padInfo'] )
    include PAD . 'events/functions.php';

  $padGetName = $name;

  if ( file_exists ( PAD . "eval/single/$kind.php" ) )
    $value = include PAD . 'eval/type/single.php';
  else
    $value = include PAD . 'eval/type/parms.php';

  $result [$k] [1] = 'VAL';
  $result [$k] [0] = $value;

  unset ( $result [$k] [2] );
  unset ( $result [$k] [3] );

  padEvalTrace ( 'type9', $result );

  padEvalOpr ( $result, $myself, $start, $end ); padEvalTrace ( 'opr4', $result );

?>