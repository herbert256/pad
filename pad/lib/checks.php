<?php

  // Two unrelated jobs: "does this application resource exist" lookups, and deciding
  // which globals belong to the application rather than to the engine.
  //
  // The lookups implement directory inheritance. padAppCheck walks the list from padDirs
  // (current page directory up to the application root) and returns the first match as a
  // path relative to APP2, so a subdirectory can override a parent. On top of it sit
  // padAppPageCheck, padAppIncludeCheck, padAppTagCheck, padAppFunctionCheck, and the
  // same idea for padScriptCheck, padCallBackCheck, padOptionCheck and padOptionEndCheck. padCommonCheck and
  // its two helpers look in the _common application instead; padCheck is the primitive
  // that accepts either a .pad or a .php file.
  //
  // padContentType sniffs a data block and names its format - list, json, yaml, xml, pad,
  // html, range, curl, file, else csv - and may trim $content to the part it recognised.
  //
  // padValidStore and padStrPad split the globals: the first says a name is application
  // data (not pad*/pq*, not a superglobal) and so may be handed to callbacks and code
  // blocks, the second says a name is engine state that a sandbox must reset, sparing the
  // padStr* machinery, the padStrSto stores, padLevelVars and the info counters.
  // padValidFirstChar is the plain "starts with a letter" test.

  function padCommonCheck  ( $check ) { 
    
    if     ( padCommonTagCheck      ( $check ) ) return TRUE;
    elseif ( padCommonIncludeCheck  ( $check ) ) return TRUE;
    else                                         return FALSE;

  }
  
  function padCommonTagCheck      ( $check ) { return padCheck    ( COMMON . "_tags/$check"    ); }
  function padCommonIncludeCheck  ( $check ) { return padCheck    ( COMMON . "_include/$check" ); }

  function padAppPageCheck        ( $check ) { return padAppCheck ( $check                     ); }
  function padAppIncludeCheck     ( $check ) { return padAppCheck ( "_include/$check"          ); }
  function padAppTagCheck         ( $check ) { return padAppCheck ( "_tags/$check"             ); }
  function padAppFunctionCheck    ( $check ) { return padAppCheck ( "_functions/$check"        ); }

  function padAppCheck ( $check ) {

    foreach ( padDirs () as $value )
      if ( padCheck ( APP2 . "$value$check" ) ) 
        return $value . $check ;

    return FALSE;

  }

  function padCheck ( $check ) {

    return ( file_exists ( "$check.pad" ) or file_exists ( "$check.php" ) ); 

  }

  function padScriptCheck ( $check ) {

    if ( ! padValidName ( $check ) )
      return FALSE;

    foreach ( padDirs () as $value )
      if ( count ( glob ( APP2 . $value . "_scripts/$check*" ) ) )
        return APP2 . $value . "_scripts/$check*";

    return FALSE;

  }

  function padCallBackCheck ( $check ) {

    if ( ! padValidName ( $check ) )
      return FALSE;

    if ( ! str_ends_with ( $check, '.php' ) )
      $check .= '.php';

    foreach ( padDirs () as $value )
      if ( file_exists ( APP2 . $value . "_callbacks/$check" ) )
        return APP2 . $value . "_callbacks/$check";

    return FALSE;

  }

  function padOptionCheck ( $check ) {

    if ( ! padValidName ( $check ) )
      return FALSE;

    foreach ( padDirs () as $value )
      if ( file_exists ( APP2 . $value . "_options/$check.php" ) )
        return APP2 . $value . "_options/$check.php";

    return FALSE;

  }

  // The end phase of an application option: _options/end/name.php runs on the rendered
  // result of the tag, where _options/name.php runs on its template before anything is
  // rendered. An option may have either, or both.

  function padOptionEndCheck ( $check ) {

    if ( ! padValidName ( $check ) )
      return FALSE;

    foreach ( padDirs () as $value )
      if ( file_exists ( APP2 . $value . "_options/end/$check.php" ) )
        return APP2 . $value . "_options/end/$check.php";

    return FALSE;

  }

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
    elseif ( substr ( $content, 0, 1 ) == '<' and strpos ( $content, '<!DOCTYPE') !== FALSE ) {
      $open   = strpos  ($content, '<!DOCTYPE');
      $close  = strpos  ($content, '>', $open);
      $check  = stripos ($content, 'pad', $open);
      if ($check !== FALSE and $check < $close )
        $type = 'pad';
      else
        $type = 'xml';
    }
    elseif ( substr ($content, 0, 5) == '<html' )
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

  // The answer per name is kept for the request: the same few hundred globals are asked
  // about again and again - every tag's PHP half snapshots the application's variables
  // before and after it runs (types/_go/tag.php), and the callbacks do the same per row.

  function padValidStore ($fld) {

    static $memo = [];

    return $memo [$fld] ??= ! padEngineName ( (string) $fld );

  }

  // The engine's names among $vars, as a set to test with isset - for the loops that sift
  // the whole symbol table, where even a memoised call per name was most of the work of a
  // page. Only names not seen before are classified; the set grows with them.

  function padEngineNames ( $vars ) {

    static $seen = [], $engine = [];

    foreach ( array_diff_key ( $vars, $seen ) as $name => $unused ) {
      $seen [$name] = TRUE;
      if ( padEngineName ( (string) $name ) )
        $engine [$name] = TRUE;
    }

    return $engine;

  }

  // Kept per name for the request, like padValidStore: every nested pass sifts the whole
  // symbol table through it twice.

  function padStrPad ( $field ) {

    static $memo = [];

    if ( isset ( $memo [$field] ) )
      return $memo [$field];

    if ( str_starts_with ( $field, 'pad' ) or str_starts_with ( $field, 'pq' ) )
      if ( ! str_starts_with ( $field, 'padStr' ) )
        if ( ! in_array ( $field, padStrSto) )
          if ( ! in_array ( $field, padLevelVars) )
            if ( $field != 'padInfoCnt' and $field != 'padInfoTraceId' )
              return $memo [$field] = TRUE;

    return $memo [$field] = FALSE;

  }

  function padValidFirstChar ($char) {

    if ( ctype_alpha ( $char) ) return TRUE;
    else                        return FALSE;

  }

?>