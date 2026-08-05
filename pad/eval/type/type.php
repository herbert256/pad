<?php

  // Resolves one TYPE token - a type-prefixed value such as field:name or data:key - into a
  // plain value.
  //
  // Included by padEvalType() with $k the token's key: [2] holds the kind and [0] the name.
  // A kind that has a file in eval/single/ is a parameterless lookup and goes through
  // eval/type/single.php; every other kind takes arguments and goes through eval/type/parms.php.
  // The token is rewritten in place as a VAL, its type slots dropped, and padEvalOpr() is
  // re-entered to carry on with the rest of the expression. $padGetName is published for the
  // handlers that read it.

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