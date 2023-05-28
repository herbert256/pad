<?php

  include 'call/call.php';

  if ( trim ( $padCallOB ) ) 

    if ( is_array ($padCallPHP) or $padCallPHP === TRUE or $padCallPHP === FALSE or $padCallPHP === NULL ) {

      if ( isset ($padExtraContent) )
        $padExtraContent .= $padCallOB;

    } else

      $padCallPHP .= $padCallOB;
 
  return $padCallPHP;

?>