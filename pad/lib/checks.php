<?php


  /**
   * Checks if content name exists in store or app.
   *
   * @param string $content Content name to check.
   *
   * @return bool TRUE if content exists.
   */
  function padContent ( $content ) {

    if ( padStoreCheck      ( $content ) ) return TRUE;
    if ( padAppContentCheck ( $content ) ) return TRUE;

    return FALSE;

  }


  /**
   * Checks if a store name exists in content store.
   *
   * @param string $store Store name to check.
   *
   * @return bool TRUE if store exists.
   */
  function padStoreCheck ( $store ) {

    return isset ( $GLOBALS ['padContentStore'] [$store] );

  }


  /** Checks if page exists in app. @see padAppCheck */
  function padAppPageCheck     ( $check ) { return padAppCheck ( $check              ); }

  /** Checks if include exists in app. @see padAppCheck */
  function padAppIncludeCheck  ( $check ) { return padAppCheck ( "_include/$check"   ); }

  /** Checks if custom tag exists in app. @see padAppCheck */
  function padAppTagCheck      ( $check ) { return padAppCheck ( "_tags/$check"      ); }

  /** Checks if custom function exists in app. @see padAppCheck */
  function padAppFunctionCheck ( $check ) { return padAppCheck ( "_functions/$check" ); }


  /**
   * Checks if a .pad or .php file exists in app directories.
   *
   * @param string $check Path relative to app directory.
   *
   * @return string|false Relative path if found, FALSE otherwise.
   */
  function padAppCheck ( $check ) {

    foreach ( padDirs () as $value )
      if ( padCheck ( APP2 . $value . $check ) )
        return $value . $check ;

    return FALSE;

  }


  /**
   * Checks if .pad or .php file exists at given path.
   *
   * @param string $check Base path without extension.
   *
   * @return bool TRUE if .pad or .php exists.
   */
  function padCheck ( $check ) {

     return  ( file_exists ( "$check.pad" ) or file_exists ( "$check.php" ) ) ;

  }


  /**
   * Checks if script files exist in _scripts directory.
   *
   * @param string $check Script name prefix.
   *
   * @return string|null Glob pattern if found.
   */
  function padScriptCheck ( $check ) {

    foreach ( padDirs () as $value )
      if ( count ( glob ( APP2 . $value . "_scripts/$check*" ) ) )
        return APP2 . $value . "_scripts/$check*";

  }


  /**
   * Detects content type from content string.
   *
   * Returns: list, json, yaml, xml, pad, html, range, curl, file, or csv.
   *
   * @param string &$content Content string (may be modified).
   *
   * @return string Content type identifier.
   */
  function padContentType ( &$content ) {

    $content = trim ( $content );

    if ( substr($content, 0, 1) == '(' and substr($content, -1) == ')' )
      $type = 'list';
    elseif ( substr ($content, 0, 6) == '&open;')
      $type = 'json';
    elseif ( substr ($content, 0, 5) == '%YAML' )
      $type = 'yaml';
    elseif ( substr ($content, 0, 3) == '---' )
      $type = 'yaml';
    elseif ( substr ( $content, 0, 5) == '<?xml')
      $type = 'xml';
    elseif ( strpos ( $content, '<!DOCTYPE') !== FALSE ) {
      $open   = strpos  ($content, '<!DOCTYPE');
      $close  = strpos  ($content, '>', $open);
      $check  = stripos ($content, 'pad', $open);
      if ($check !== FALSE and $check < $close )
        $type = 'pad';
      else
        $type = 'xml';
    }
    elseif ( substr ($content, 9, 5) == '<html' )
      $type = 'html';
    elseif ( substr($content, 0, 1) == '<')
      $type = 'xml';
    elseif ( substr($content, 0, 1) == '{')
      $type = 'json';
    elseif ( substr($content, 0, 1) == '[')
      $type = 'json';
    elseif ( substr($content, 0, 1) == '(')
      $type = 'json';
    elseif ( substr($content, -1) == ')')
      $type = 'json';
    else
      $type = '';

    if ( $type )
      return $type;

    $first = strpos ($content, '({');
    $last  = strpos ($content, '})');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

    $first = strpos ($content, '([');
    $last  = strpos ($content, '])');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

    $parts = padExplode ($content, '..');
    if ( count ($parts) == 2 and ctype_alnum($parts[0]) and ctype_alnum($parts[1]) )
      return 'range';

    if ( str_starts_with ( strtolower ( $content ), 'http:' )
      or str_starts_with ( strtolower ( $content ), 'https:' )  )
      return 'curl';

    if ( padDataFileName ( $content ) )
      return 'file';

    return 'csv';

  }


  /**
   * Checks if field name is valid for app storage.
   *
   * Rejects pad*, pq*, and PHP superglobals.
   *
   * @param string $fld Field name.
   *
   * @return bool TRUE if valid for storage.
   */
  function padValidStore ($fld) {

    if ( substr($fld, 0,3) == 'pad' )
      return FALSE;

    if ( substr($fld, 0,2) == 'pq' )
      return FALSE;

    if ( in_array ( $fld, ['GLOBALS','_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ) )
      return FALSE;

    return TRUE;

  }


  /**
   * Checks if field is internal PAD variable (non-storable).
   *
   * @param string $field Field name.
   *
   * @return bool TRUE if internal PAD variable.
   */
  function padStrPad ( $field ) {

    if ( str_starts_with ( $field, 'pad' ) or str_starts_with ( $field, 'pq' ) )
      if ( ! str_starts_with ( $field, 'padStr' ) )
        if ( ! in_array ( $field, padStrSto) )
          if ( ! in_array ( $field, padLevelVars) )
            if ( $field <> 'padInfoCnt' and $field <> 'padInfoTraceId' )
              return TRUE;

    return FALSE;

  }


  /**
   * Checks if character is valid as first char of identifier.
   *
   * @param string $char Single character.
   *
   * @return bool TRUE if alphabetic.
   */
  function padValidFirstChar ($char) {

    if ( ctype_alpha ( $char) ) return TRUE;
    else                        return FALSE;

  }


?>
