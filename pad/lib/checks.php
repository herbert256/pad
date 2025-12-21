<?php

  function padAppPageCheck     ( $check ) { return padAppCheck ( $check              ); }

  function padAppIncludeCheck  ( $check ) { return padAppCheck ( "_include/$check"   ); }

  function padAppTagCheck      ( $check ) { return padAppCheck ( "_tags/$check"      ); }

  function padAppFunctionCheck ( $check ) { return padAppCheck ( "_functions/$check" ); }

  function padAppCheck ( $check ) {

    foreach ( padDirs () as $value )
      if ( padCheck ( APP2 . $value . $check ) )
        return $value . $check ;

    return FALSE;

  }

  function padCheck ( $check ) {

     return  ( file_exists ( "$check.pad" ) or file_exists ( "$check.php" ) ) ;

  }

  function padScriptCheck ( $check ) {

    foreach ( padDirs () as $value )
      if ( count ( glob ( APP2 . $value . "_scripts/$check*" ) ) )
        return APP2 . $value . "_scripts/$check*";

  }

  function padCallBackCheck ( $check ) {

    if ( ! str_ends_with ( $check, '.php' ) )
      $check .= '.php';

    foreach ( padDirs () as $value )
      if ( file_exists ( APP2 . $value . "_callbacks/$check" ) )
        return APP2 . $value . "_callbacks/$check";

    return FALSE;

  }

  function padOptionCheck ( $check ) {

    foreach ( padDirs () as $value )
      if ( file_exists ( APP2 . $value . "_options/$check.php" ) )
        return APP2 . $value . "_options/$check.php";

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

  function padValidStore ($fld) {

    if ( substr($fld, 0,3) == 'pad' )
      return FALSE;

    if ( substr($fld, 0,2) == 'pq' )
      return FALSE;

    if ( in_array ( $fld, ['GLOBALS','_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ) )
      return FALSE;

    return TRUE;

  }

  function padStrPad ( $field ) {

    if ( str_starts_with ( $field, 'pad' ) or str_starts_with ( $field, 'pq' ) )
      if ( ! str_starts_with ( $field, 'padStr' ) )
        if ( ! in_array ( $field, padStrSto) )
          if ( ! in_array ( $field, padLevelVars) )
            if ( $field <> 'padInfoCnt' and $field <> 'padInfoTraceId' )
              return TRUE;

    return FALSE;

  }

  function padValidFirstChar ($char) {

    if ( ctype_alpha ( $char) ) return TRUE;
    else                        return FALSE;

  }

?>