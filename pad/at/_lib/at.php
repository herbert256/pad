<?php


  /**
   * Resolves an @ field reference to its value.
   *
   * Entry point for the @ field resolution system. Validates the field syntax
   * and delegates to padAtValue() for actual resolution. Returns INF if the
   * field cannot be resolved.
   *
   * @param string $field The @ field reference (e.g., "name@level", "var@*").
   * @param int    $cor   Level correction offset, default 0.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAt ( $field, $cor=0 ) {

    if ( ! padAtCheck ( $field ) )
      return INF;

    if ( str_ends_with ( $field, '@' ) )
      $field .= '*';

    return padAtValue ( $field, $cor );

  }

        
  /**
   * Resolves an @ field reference by trying multiple resolution strategies.
   *
   * Parses the field into name and level components, then tries resolution in order:
   * tag lookup, level group, properties, groups, type, store, globals, and data.
   * Returns the first successful match or INF if nothing found.
   *
   * @param string $field The @ field reference to resolve.
   * @param int    $cor   Level correction offset, default 0.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtValue ( $field, $cor=0 ) {

    padSplit ( '@', $field, $before, $after );
  
    $names = padExplode ( $before, '.' ); 
    $name  = reset ($names);

    $GLOBALS ['padForceTagName']  = $name;
    $GLOBALS ['padForceDataName'] = $name;

    if ( str_contains($field, '@*') )
      return padAtValueAny ( $field, $cor);

    $parts = padExplode ( $after,  ':' ); 

    if ( ! count ( $parts ) )
      return padAtSingle ( $field, $cor );

    $first  = $parts [0] ?? '';
    $second = $parts [1] ?? '';

    $check = padAtTag ( $name, $names, $first, $second, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtLevelGroup ( $name, $names, $first, $second, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtProperties ( $name, $names, $first, $second, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtGroups ( $name, $names, $first, $second, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtType ( $first, $second, $names, $cor );
    if ( $check !== INF )
      return $check;

    $check = padAtStore ( $GLOBALS ['pqStore'] ?? [], $names, $parts );
    if ( $check !== INF )
      return $check;

    $check = padAtStore ( $GLOBALS ['padDataStore'] ?? [], $names, $parts );
    if ( $check !== INF )
      return $check;

    $check = padAtGlobals ( $names, $parts );
    if ( $check !== INF )
      return $check;

    $check = padAtData ( $names, $parts );
    if ( $check !== INF )
      return $check;

    return INF;

  }


  /**
   * Resolves an @ field reference containing a wildcard (*).
   *
   * Iterates through all levels from current to root, replacing @* with the
   * actual level index until a match is found. Falls back to single resolution
   * if no level-specific match is found.
   *
   * @param string $field The @ field reference with wildcard (e.g., "name@*").
   * @param int    $cor   Level correction offset.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function  padAtValueAny ( $field, $cor ) {

    global $pad;

    $check = str_replace ( '@*', '', $field );

    if ( file_exists ( PAD . "at/properties/$check.php") ) {
      $idx   = padFieldFirstNonTag ( $cor ) + 1 ;
      $check = str_replace ( '@*', "@$idx", $field );
      $check = padAtValue ( $check, $cor );
      if ( $check !== INF )
        return $check; 
    }

    for ( $i=$pad; $i>-1; $i-- ) {
 
      $check = str_replace ( '@*', "@$i", $field );
      $check = padAtValue ( $check, $cor );
      if ( $check !== INF )
        return $check; 

    }

    if ( str_ends_with($field, '@*') ) {

      $check = padAtSingle ( $field, $cor );
      if ( $check !== INF )
        return $check;
      
    }

    return INF;

  }
  

  /**
   * Resolves a simple @ field reference without level specifier.
   *
   * Handles fields that reference a name without explicit level targeting,
   * delegating to the other types resolution system.
   *
   * @param string $field The @ field reference.
   * @param int    $cor   Level correction offset.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtSingle ( $field, $cor ) {

    $field = str_replace ( '@*', '', $field );
    $names = padExplode ( $field, '.' ); 
    $name  = reset ( $names );
    $type  = '';

    $check = include PAD . 'at/types/_lib/other.php';
    if ( $check !== INF )
      return $check;
   
    return INF;

  }


  /**
   * Searches PHP superglobals for an @ field reference.
   *
   * Checks _POST, _GET, _COOKIE, _SESSION, _FILES, _SERVER, _REQUEST, and _ENV
   * arrays for a matching value based on the provided name path.
   *
   * @param array $names Array of name segments forming the lookup path.
   * @param int   $cor   Level correction offset (unused in this function).
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtSpecial ( $names, $cor ) {

    $special = ['_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ;

    foreach ( $special as $field ) 

      if ( isset ( $GLOBALS [$field] ) and is_array ( $GLOBALS [$field] ) and count ( $GLOBALS [$field] )) {
        $check = padAtSearch ( $GLOBALS [$field], $names );
        if ( $check !== INF)
          return $check;
      }

    return INF;

  }


  /**
   * Searches PAD framework globals for an @ field reference.
   *
   * Iterates through all global variables starting with 'pad' and searches
   * their array values for a matching path.
   *
   * @param array $names Array of name segments forming the lookup path.
   * @param int   $cor   Level correction offset (unused in this function).
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtPad ( $names, $cor ) {

    foreach ( $GLOBALS  as $key => $value ) 

      if ( str_starts_with($key, 'pad' ) and is_array ( $GLOBALS [$key] ) and count ( $GLOBALS [$key] ) ) {
        $check = padAtSearch ( $value, $names );
        if ( $check !== INF)
          return $check;
      }

    return INF;

  }
  

  /**
   * Searches a data store for an @ field reference.
   *
   * Looks up values in a store array based on the provided parts (level specifiers)
   * and name path. Handles sequence, sequences, and data store types.
   *
   * @param array $store The data store to search.
   * @param array $names Array of name segments forming the lookup path.
   * @param array $parts Array of level/type specifiers from the @ reference.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtStore ( $store, $names, $parts ) {

    if ( ! count ($store) ) 
      return INF;

    if ( count ($parts) == 1 ) {      
      $name = reset ($parts);
      if ( isset ( $store [$name] ) )
        return padAtSearch ( $store [$name], $names );
    }

    if ( count ($parts) == 2 ) {
      list ( $first, $second ) = $parts;
      if ( $first == 'sequence' or $first == 'sequences' or $first == 'data' )
        if ( isset ( $store [$second] ) )
          return padAtSearch ( $store [$second], $names );
    }

    if ( count ($parts) == 1 ) {      
      $name = reset ($parts);
      if ( $name == 'sequence' or $name == 'sequences' or $name == 'data' )
        return padAtSearch ( $store, $names );
    }

    return INF;

  }


  /**
   * Searches PHP globals for an @ field reference.
   *
   * Entry point for global variable lookup. Requires exactly one part
   * in the level specifier and delegates to padAtGlobals2() for recursive search.
   *
   * @param array $names Array of name segments forming the lookup path.
   * @param array $parts Array containing the global variable name to search.
   *
   * @return mixed The resolved value, or INF if not found or invalid parts.
   */
  function padAtGlobals ( $names, $parts ) {

    if ( count ($parts) <> 1 )
      return INF;

    $array = reset ($parts);

    return padAtglobals2 ( $array, $names, $GLOBALS );

  }


  /**
   * Recursively searches a global variable and its nested stores.
   *
   * Checks if the named array exists in the search scope, then recursively
   * searches any valid nested stores for the value path.
   *
   * @param string $array  The array key name to find.
   * @param array  $names  Array of name segments forming the lookup path.
   * @param array  $search The current search scope (starts with $GLOBALS).
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtGlobals2 ( $array, $names, $search ) {

    if ( isset ( $search [$array] ) and is_array ( $search [$array] ) ) {
      $check = padAtSearch ( $search [$array], $names, 1 );
      if ( $check !== INF ) 
        return $check;
    }

    foreach ( $search as $k => $v )
      if ( padValidStore ($k) and is_array ($v) ) {
        $check = padAtGlobals2 ( $array, $names, $v );
        if ( $check !== INF ) 
          return $check;
      }

    return INF;

  }


  /**
   * Resolves an @ reference at a specific tag level.
   *
   * Looks up a value by tag name at a specific level. Returns early if
   * a second part is specified (invalid for tag lookup).
   *
   * @param string $name   The tag name to look up.
   * @param array  $names  Array of name segments forming the lookup path.
   * @param string $first  The first part of the level specifier.
   * @param string $second The second part of the level specifier (must be empty).
   * @param int    $cor    Level correction offset.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtTag ( $name, $names, $first, $second, $cor ) {

    if ( $second )
      return INF;

    $padIdx = padAtIdx ( $first, $cor );

    return include PAD . 'at/any/tag.php';

  }


  /**
   * Resolves an @ reference for a specific level and group combination.
   *
   * Combines level index lookup with group-based resolution for @ references
   * that specify both a level and a group type.
   *
   * @param string $name  The variable name to look up.
   * @param array  $names Array of name segments forming the lookup path.
   * @param string $level The level specifier (name or index).
   * @param string $group The group type specifier.
   * @param int    $cor   Level correction offset.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtLevelGroup ( $name, $names, $level, $group, $cor ) {  

    $padIdx = padAtIdx ( $level, $cor );

    return padAtGroup ( $group, $name, $names, $padIdx );

  }


  /**
   * Searches all levels for an @ reference matching a group type.
   *
   * When no second specifier is given and the first matches a group file,
   * iterates through all levels from current to root looking for a match.
   *
   * @param string $name   The variable name to look up.
   * @param array  $names  Array of name segments forming the lookup path.
   * @param string $first  The group type specifier.
   * @param string $second Secondary specifier (must be empty).
   * @param int    $cor    Level correction offset.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtGroups ( $name, $names, $first, $second, $cor ) {

    global $pad;

    if ( ! $second and file_exists ( PAD . "at/groups/$first.php") )

      for ( $i=$pad; $i>-1; $i-- ) {
        $check = padAtGroup ( $first, $name, $names, $i+$cor );
        if ( $check !== INF )
          return $check;
      }

    return INF;

  }


  /**
   * Resolves an @ reference using a specific group handler at a level.
   *
   * Includes the appropriate group handler file to resolve the value.
   * Fires an info event if debugging is enabled.
   *
   * @param string $group  The group type (e.g., "data", "options").
   * @param string $name   The variable name to look up.
   * @param array  $names  Array of name segments forming the lookup path.
   * @param int    $padIdx The level index to search at.
   *
   * @return mixed The resolved value, or INF if not found or invalid.
   */
  function padAtGroup ( $group, $name, $names, $padIdx ) {

    if ( ! $group or ! $padIdx or ! file_exists ( PAD . "at/groups/$group.php" ) )
      return INF;

    if ( $GLOBALS ['padInfo'] ) 
      include PAD . 'events/atGroups.php'; 

    return include PAD . "at/groups/$group.php";

  }


  /**
   * Resolves an @ reference to a property at a specific level.
   *
   * Handles property lookups when no second specifier is given.
   * Delegates to padAtProperty() for actual resolution.
   *
   * @param string $name   The property name to look up.
   * @param array  $names  Array of name segments forming the lookup path.
   * @param string $first  The level specifier.
   * @param string $second Secondary specifier (must be empty).
   * @param int    $cor    Level correction offset.
   *
   * @return mixed The resolved value, or INF if not found.
   */
  function padAtProperties ( $name, $names, $first, $second, $cor ) {

    if ( $second )
      return INF;
    
    $padIdx = padAtIdx ( $first, $cor );

    $current = padAtProperty ( $names, $padIdx );
    if ( $current !== INF ) 
      return $current;

    return INF;

  }


  /**
   * Resolves an @ reference to a specific property file handler.
   *
   * Loads the property handler from at/properties/ and returns its result.
   * Fires an info event if debugging is enabled.
   *
   * @param array $names  Array of name segments (first is property name, second is optional param).
   * @param int   $padIdx The level index to query.
   *
   * @return mixed The resolved property value, or INF if not found.
   */
  function padAtProperty ( $names, $padIdx ) {

    if     ( count ( $names ) == 1 ) $parm = '';
    elseif ( count ( $names ) == 2 ) $parm = end ($names);
    else                             return INF;

    $name = reset ( $names );

    if ( ! $padIdx or ! file_exists ( PAD . "at/properties/$name.php") ) 
      return INF;

    $return = include PAD . "at/properties/$name.php";

    if ( $return !== INF )
      padAtSetTag ();

    if ( $GLOBALS ['padInfo'] ) 
      include PAD . 'events/atProperties.php'; 

    return $return;

  }


  /**
   * Resolves an @ reference using a type-specific handler.
   *
   * Loads the appropriate type handler from at/types/ directory.
   * Fires an info event if debugging is enabled.
   *
   * @param string $go    The type handler name.
   * @param string $type  Additional type specifier.
   * @param array  $names Array of name segments forming the lookup path.
   * @param int    $cor   Level correction offset.
   *
   * @return mixed The resolved value, or INF if handler not found.
   */
  function padAtType ( $go, $type, $names, $cor ) {

    if ( ! file_exists ( PAD . "at/types/$go.php" ) )
      return INF;

    if ( $GLOBALS ['padInfo'] ) 
      include PAD . 'events/atTypes.php'; 

    return include PAD . "at/types/$go.php";

  }


  /**
   * Resolves an @ reference by loading data from the data store.
   *
   * First checks if the data is already in the store, otherwise loads it
   * using padData() and caches the result for future lookups.
   *
   * @param array $names Array of name segments forming the lookup path.
   * @param array $parts Array of level specifiers joined to form the data key.
   *
   * @return mixed The resolved value, or INF if not found.
   *
   * @global array $padDataStore Cache of loaded data sources.
   */
  function padAtData ( $names, $parts ) {

    global $padDataStore;

    $data = implode ( ':', $parts );

    if ( isset ( $padDataStore [$data] ) )

      $check = padAtSearch ( $padDataStore [$data], $names );
    
    else {

      $check = padAtDataNew ( $data, $names );

      if ( $check !== INF ) 
        return $check;

    }

    return $check;

  }


?>