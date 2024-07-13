<?php

   $padCall = $padParm;

   $padCallReturn = include pad . 'call/anyNoOne.php';

    if ( is_array ($padCallReturn) or is_object ($padCallReturn) or is_resource ($padCallReturn) )
      return $padCallReturn;

    if ( $padCallReturn === TRUE or $padCallReturn === FALSE or $padCallReturn === NULL ) 
      return $padCallReturn;

   $padContent = $padCallReturn . $padContent;

   return TRUE;

?>