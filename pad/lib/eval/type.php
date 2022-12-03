<?php
 

  function padEvalType ( $type, $left, &$result, $myself, $start, $end=999999 ) {

    if ( file_exists ( PAD . 'eval/single/' . $result [$type] [2] . '.php' ) )
      padEvalSingle ( $result, result [$type] [2] );
    else
      padEvalParms  ( $type, $left, $result, $myself, $start, $end ) ;
   
  }


  function padArraySingle ($value) {

    $value = padToArray ($value);

    $array = [];

    foreach ( $value as $v1 )
      if ( ! is_array($v1) )
        $array [] = $v1;
      else 
        foreach ( $v1 as $v2 ) {
          if ( ! is_array($v2) )
             $array [] = $v2;
          else 
            foreach ( $v2 as $v3 ) {
              if ( ! is_array($v3) )
                $array [] = $v3;
              break;
            }
          break;
        }

    return $array;

  }


?>