<?php

  // Returns the key that follows $key in $arr, or 0 when there is none. Token keys are
  // sparse - they start at 100 and step by 100, and stages unset tokens as they collapse
  // them - so the next token cannot be found by adding to the key. Used by
  // eval/type/parms.php to find where a function call's parameters begin.

  function padEvalNextKey ( $arr, $key ) {

    $keys = array_keys ( $arr );
    $pos  = array_search ( $key, $keys );

    if ( $pos !== FALSE and isset ( $keys [ $pos + 1 ] ) )
      return $keys [ $pos + 1 ];
    else
      return 0;

  }

?>