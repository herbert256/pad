<?php

  // What the cells of an example show, worked out from the page itself: the declarations
  // its tags reach, and the statements the database was sent.

  // The tables a template reaches: every tag that names a declared table - bare or with the
  // select: prefix - and the tables those stand on, join and add by union.

  function psTablesUsed ( $template, $selects ) {

    preg_match_all ( '/\{(?:select:)?([A-Za-z_][A-Za-z0-9_]*)/', $template, $found );

    $todo = array_values ( array_unique ( array_filter ( $found [1], fn ( $name ) => isset ( $selects [$name] ) ) ) );
    $used = [];

    while ( $todo ) {

      $name = array_shift ( $todo );

      if ( in_array ( $name, $used, TRUE ) )
        continue;

      $used [] = $name;
      $one     = $selects [$name] ?? [];
      $more    = [];

      if ( is_string ( $one ['base'] ?? NULL ) )
        $more [] = $one ['base'];

      foreach ( (array) ( $one ['union'] ?? [] ) as $member )
        $more [] = $member;

      if ( is_array ( $one ['join'] ?? NULL ) )
        foreach ( is_array ( reset ( $one ['join'] ) ) ? $one ['join'] : [ $one ['join'] ] as $join )
          $more [] = reset ( $join );

      foreach ( $more as $next )
        if ( is_string ( $next ) and isset ( $selects [$next] ) )
          $todo [] = $next;

    }

    return $used;

  }

  // The declarations of those tables as PHP, and the relations between any two of them.

  function psDeclarations ( $tables, $selects, $relations ) {

    $width = max ( array_map ( 'strlen', $tables ?: [ '' ] ) );
    $lines = [];

    foreach ( $tables as $name )
      $lines [] = '$padSelect [' . str_pad ( "'$name']", $width + 3 ) . ' = ' . psExport ( $selects [$name] ) . ';';

    $pairs = [];

    foreach ( $relations as $from => $list )
      foreach ( $list as $to => $how )
        if ( in_array ( $from, $tables, TRUE ) and in_array ( $to, $tables, TRUE ) )
          $pairs [] = [ $from, $to, $how ];

    if ( $pairs ) {

      $lines [] = '';

      $wide = max ( array_map ( fn ( $one ) => strlen ( $one [0] ), $pairs ) );

      foreach ( $pairs as [ $from, $to, $how ] )
        $lines [] = '$padRelations [' . str_pad ( "'$from']", $wide + 3 ) . " ['$to'] = " . psExport ( $how ) . ';';

    }

    return implode ( "\n", $lines );

  }

  // A value written the way the declarations are: [ 'key' => 'value', ... ] on one line.

  function psExport ( $value ) {

    if ( is_array ( $value ) ) {

      $list  = array_is_list ( $value );
      $items = [];

      foreach ( $value as $key => $one )
        $items [] = ( $list ? '' : psExport ( $key ) . ' => ' ) . psExport ( $one );

      return '[ ' . implode ( ', ', $items ) . ' ]';

    }

    if ( is_string ( $value ) )
      return str_contains ( $value, "'" ) ? '"' . addcslashes ( $value, '"\\$' ) . '"' : "'$value'";

    return var_export ( $value, TRUE );

  }

  // The statements as the database got them, white space made single, each shape once: a
  // nested table sends one statement per row around it, the same but for the quoted value
  // it follows. The first of a shape stands for it, with how many there were.

  function psStatements ( $statements ) {

    $shapes = [];

    foreach ( $statements as $one ) {

      $one   = trim ( preg_replace ( '/\s+/', ' ', $one ) );
      $shape = preg_replace ( "/'(?:[^'\\\\]|\\\\.|'')*'/", '?', $one );

      $shapes [$shape] ??= [ 'sql' => $one, 'times' => 0 ];
      $shapes [$shape] ['times']++;

    }

    return array_values ( $shapes );

  }

?>
