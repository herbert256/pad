<?php

  // {debug $order} and {debug}: a value shown where the page is, while rendering goes on -
  // for a local request only (padLocal: the command line, or loopback with nothing
  // forwarded, and $padDiagnostics not switched off). A remote visitor gets nothing.
  //
  // The tag's items are read raw, as {attrs} reads them: a $name that holds an array is the
  // array, where an expression would read it as empty, and a missing name is shown as
  // missing instead of ending the request under the strict check. Without items it shows
  // every field visible where it stands - the rows of the enclosing levels, innermost
  // first - and the application's own variables.

  function padDebug () {

    global $pad, $padCurrent;

    if ( ! padLocal () )
      return '';

    $items = padAttrsItems ();
    $out   = '';

    if ( ! $items ) {

      $fields = [];

      for ( $i = $pad - 1; $i > 0; $i-- )
        foreach ( (array) ( $padCurrent [$i] ?? [] ) as $key => $value )
          if ( ! array_key_exists ( $key, $fields ) )
            $fields [$key] = $value;

      $app = [];

      foreach ( $GLOBALS as $key => $value )
        if ( ! preg_match ( '/^(pad|pq|_|GLOBALS$|argv$|argc$)/', $key ) and ! is_object ( $value ) )
          $app [$key] = $value;

      ksort ( $app );

      return padDebugBox ( 'fields here', $fields, TRUE ) . padDebugBox ( 'application variables', $app, TRUE );

    }

    foreach ( $items as [ $name, $expr ] ) {

      $label = ( $name !== '' ) ? $name : $expr;

      if ( preg_match ( '/^\$([A-Za-z_][A-Za-z0-9_.:@]*)$/', trim ( $expr ), $match ) ) {
        $field = $match [1];
        if     ( padArrayCheck ( $field ) ) $value = padArrayValue ( $field );
        elseif ( padFieldCheck ( $field ) ) $value = padFieldValue ( $field );
        else {
          $out .= padDebugBox ( $label, INF, FALSE );
          continue;
        }
      } else
        $value = padEval ( $expr );

      $out .= padDebugBox ( $label, $value, is_array ( $value ) );

    }

    return $out;

  }

  function padDebugBox ( $label, $value, $open ) {

    return '<details class="pad-debug"' . ( $open ? ' open' : '' ) . '><summary>'
         . htmlspecialchars ( $label ) . ' <small>' . padDebugType ( $value ) . '</small></summary>'
         . padDebugTree ( $value ) . '</details>';

  }

  function padDebugType ( $value ) {

    if ( $value === INF   ) return 'missing';
    if ( is_array ( $value ) ) return 'array(' . count ( $value ) . ')';
    if ( is_object ( $value ) ) return get_class ( $value );
    if ( is_null ( $value ) ) return 'null';
    if ( is_bool ( $value ) ) return 'bool';
    if ( is_int ( $value ) ) return 'int';
    if ( is_float ( $value ) ) return 'float';

    return 'string(' . mb_strlen ( (string) $value ) . ')';

  }

  function padDebugTree ( $value, $depth = 0 ) {

    if ( $value === INF )
      return '';

    if ( is_object ( $value ) )
      $value = padToArray ( $value );

    if ( ! is_array ( $value ) )
      return '<pre>' . htmlspecialchars ( var_export ( $value, TRUE ) ) . '</pre>';

    if ( $depth > 8 )
      return '<pre>…</pre>';

    $out = '<ul>';

    foreach ( $value as $key => $one )
      if ( is_array ( $one ) or is_object ( $one ) )
        $out .= '<li><details' . ( $depth < 1 ? ' open' : '' ) . '><summary>' . htmlspecialchars ( (string) $key )
              . ' <small>' . padDebugType ( $one ) . '</small></summary>' . padDebugTree ( $one, $depth + 1 ) . '</details></li>';
      else
        $out .= '<li>' . htmlspecialchars ( (string) $key ) . ': <code>'
              . htmlspecialchars ( var_export ( $one, TRUE ) ) . '</code></li>';

    return $out . '</ul>';

  }

?>
