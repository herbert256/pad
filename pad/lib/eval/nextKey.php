<?php


  /**
   * Gets the next key after a given key in an array.
   *
   * Finds the position of the given key and returns the key
   * that follows it. Returns 0 if key not found or is last.
   *
   * @param array $arr The array to search.
   * @param mixed $key The current key.
   *
   * @return mixed The next key, or 0 if none.
   */
  function padEvalNextKey ( $arr, $key ) {

    $keys = array_keys ( $arr );
    $pos  = array_search ( $key, $keys );

    if ( $pos !== FALSE and isset ( $keys [ $pos + 1 ] ) )
      return $keys [ $pos + 1 ];
    else
      return 0;

  }

?>