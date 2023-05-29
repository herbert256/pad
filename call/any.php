<?php

  if ( ! padExists ( $padCall ) )
    return '';
  
  include pad . 'call/_call.php';

  if ( trim ( $padCallOB ) ) 

    if ( is_array ($padCallPHP) or $padCallPHP === TRUE or $padCallPHP === FALSE or $padCallPHP === NULL ) {

      if ( isset ($padExtraContent) )
        $padExtraContent .= $padCallOB;

    } else

      $padCallPHP .= $padCallOB;
 
  return $padCallPHP;

?>