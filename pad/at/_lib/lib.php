<?php


  /**
   * Loads new data into the data store and searches it for a value.
   *
   * Loads data using padData(), caches it in $padDataStore, then searches
   * for the requested value. Removes from cache if no match is found.
   *
   * @param string $name  The data source name to load.
   * @param array  $names Array of name segments forming the lookup path.
   *
   * @return mixed The resolved value, or INF if not found.
   *
   * @global array $padDataStore Cache of loaded data sources.
   */
   function padAtDataNew ( $name, $names ) {

    global $padDataStore;

    $padDataStore [$name] = padData ($name);

    $check = padAtSearch ( $padDataStore [$name], $names ); 

    if ( $check === INF ) 
      unset ( $padDataStore [$name] );

    return $check;

  }


  /**
   * Converts a level specifier to an actual level index.
   *
   * Accepts level names, negative relative offsets (e.g., "-1" for previous level),
   * numeric indices, or tag names. Searches from current level to root.
   *
   * @param string $level The level specifier (name, tag, or index).
   * @param int    $cor   Level correction offset.
   *
   * @return int|false The resolved level index, or FALSE if not found.
   *
   * @global int   $pad     Current level index.
   * @global array $padName Array of level names.
   * @global array $padTag  Array of tag names by level.
   */
  function padAtIdx ( $level, $cor ) {

    global $pad, $padName, $padTag;

    for ( $i=$pad; $i>-1; $i-- ) 
      if ( $padName [$i] == $level )
        return $i;

    if ( strlen($level) > 1 and substr($level,0,1) == '-' and is_numeric(substr($level,1)) ) {
      $idx = $pad + $level;
      if ( $cor )
        $idx = $idx + $cor;
      if ( $idx > 0 and $idx <= $pad )
        return $idx;

    }

    if ( is_numeric ($level) ) 
      if ($level >= 0 and $level <= $pad )
        return $level;

    for ( $i=$pad; $i>-1; $i-- ) 
      if ( $padTag [$i] == $level )
        return $i;

    return FALSE;
    
  }


  /**
   * Gets an associative array key by numeric index.
   *
   * For non-list arrays, returns the key at the given 1-based index position.
   * Returns empty string for list arrays or if the key exists directly.
   *
   * @param array  $search The array to get the key from.
   * @param string $index  The 1-based numeric index.
   *
   * @return string The key at the index position, or empty string.
   */
  function padAtKey ( $search, $index ) {

    if ( ! ctype_digit ( $index ) )             return '';
    if ( array_is_list ( $search ) )            return '';
    if ( array_key_exists ( $index, $search ) ) return '';

    $keys = array_keys ( $search );

    return $keys [ $index - 1 ] ?? '';

  }


  /**
   * Sets the current level type to 'tag' after a successful @ resolution.
   *
   * Called after resolving @ properties to update the level type tracking,
   * ensuring proper handling in subsequent operations.
   *
   * @return void
   *
   * @global int   $pad     Current level index.
   * @global array $padType Array of type identifiers by level.
   */
  function padAtSetTag () {

    global $pad, $padType;

    #if ( $padType [$pad] == 'field' or $padType [$pad] == 'array' )
      $padType [$pad] = 'tag' ;

  }


?>