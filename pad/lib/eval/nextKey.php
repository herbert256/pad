<?php

  function padEvalNextKey ( $arr, $key ) {

    $keys = array_keys ( $arr );
    $pos  = array_search ( $key, $keys );

    if ( $pos !== FALSE and isset ( $keys [ $pos + 1 ] ) )
      return $keys [ $pos + 1 ];
    else
      return 0;

  }

?>
