<?php

  // TypeScript types of PAD data: what a page's variables and its JSON answer hold, written
  // as declarations a component can import - the contract between the PHP that makes the
  // data and the script that reads it, made from the data itself. pad types (apps/cli) writes
  // them for the pages of an application from their samples and JSON answers.
  //
  //   padTypeScript ( [ 'cart' => [ 'count' => 2, 'lines' => [ ... ] ] ] )
  //     { cart: { count: number; lines: { id: number; name: string }[] } }
  //
  // padTypeScript           the type of a value: null, boolean, number, string, a list as
  //                         T[], an array with keys - or an object - as { key: T }
  // padTypeScriptInterface  export interface Name { ... } for an array with keys
  // padTypeScriptName       a page's name as a type name: examples/shared is ExamplesShared
  // padTypeScriptList       the type of a list's items: the rows of a list of rows are one
  //                         type, a key some rows lack optional (key?:), the types of a key
  //                         in different rows a union
  // padTypeScriptRow        whether a value is a row - an array with keys, or an object
  // padTypeScriptKey        a key as TypeScript writes it - quoted when it is no identifier
  //
  // A list without items is unknown[] - its rows cannot be seen; an empty object Record<string,
  // unknown>. JSON decoded without assoc keeps the difference between the two: [] and {}.

  function padTypeScript ( $value, $indent = '' ) {

    if ( $value === NULL )
      return 'null';

    if ( is_bool ( $value ) )
      return 'boolean';

    if ( is_int ( $value ) or is_float ( $value ) )
      return 'number';

    if ( is_string ( $value ) )
      return 'string';

    if ( is_object ( $value ) )
      $value = get_object_vars ( $value ) ?: new ArrayObject;

    if ( $value instanceof ArrayObject )
      return 'Record<string, unknown>';

    if ( ! is_array ( $value ) )
      return 'unknown';

    if ( array_is_list ( $value ) )
      return padTypeScriptList ( $value, $indent );

    return padTypeScriptObject ( array_map ( fn ( $one ) => [ $one, TRUE ], $value ), $indent );

  }

  function padTypeScriptInterface ( $name, $value ) {

    $type = padTypeScript ( $value );

    if ( ! str_starts_with ( $type, '{' ) )
      return "export type $name = $type;\n";

    return "export interface $name $type\n";

  }

  function padTypeScriptName ( $page, $suffix = '' ) {

    $name = str_replace ( ' ', '', ucwords ( preg_replace ( '/[^A-Za-z0-9]+/', ' ', (string) $page ) ) );

    if ( $name === '' or ctype_digit ( $name [0] ) )
      $name = 'Page' . $name;

    return $name . $suffix;

  }

  // The items of a list. Rows - arrays with keys, objects - become one object type; any other
  // items the union of their types; a list of both, the union of the row type and the rest.

  function padTypeScriptList ( $list, $indent ) {

    if ( ! $list )
      return 'unknown[]';

    $rows  = [];
    $other = [];

    foreach ( $list as $item ) {

      if ( is_object ( $item ) )
        $item = get_object_vars ( $item );

      if ( is_array ( $item ) and $item and ! array_is_list ( $item ) )
        $rows [] = $item;
      else
        $other [ padTypeScript ( $item, $indent ) ] = TRUE;

    }

    $types = array_keys ( $other );

    if ( $rows ) {

      $keys = [];

      foreach ( $rows as $row )
        foreach ( $row as $key => $one )
          $keys [$key] [] = $one;

      $fields = [];

      foreach ( $keys as $key => $values )
        $fields [$key] = [ $values, count ( $values ) == count ( $rows ) ];

      array_unshift ( $types, padTypeScriptObject ( $fields, $indent, TRUE ) );

    }

    $type = implode ( ' | ', $types );

    return ( count ( $types ) > 1 ? "($type)" : $type ) . '[]';

  }

  // An object type, a field per key: [ key => [ value, always there ] ], or with $many
  // [ key => [ the values of the rows, always there ] ] - their types made one union.

  function padTypeScriptObject ( $fields, $indent, $many = FALSE ) {

    if ( ! $fields )
      return 'Record<string, unknown>';

    $inner = "$indent  ";
    $lines = [];

    foreach ( $fields as $key => [ $values, $always ] ) {

      $types = [];

      // The rows of the rows: one object type made of them all, as a list of rows makes it.

      if ( $many and count ( $values ) > 1 and ! array_filter ( $values, fn ( $one ) => ! padTypeScriptRow ( $one ) ) )
        $types [ substr ( padTypeScriptList ( $values, $inner ), 0, -2 ) ] = TRUE;

      else
        foreach ( $many ? $values : [ $values ] as $one )
          $types [ padTypeScript ( $one, $inner ) ] = TRUE;

      $lines [] = $inner . padTypeScriptKey ( (string) $key ) . ( $always ? '' : '?' ) . ': ' . implode ( ' | ', array_keys ( $types ) ) . ';';

    }

    return "{\n" . implode ( "\n", $lines ) . "\n$indent}";

  }

  function padTypeScriptRow ( $value ) {

    if ( is_object ( $value ) )
      $value = get_object_vars ( $value );

    return is_array ( $value ) and $value and ! array_is_list ( $value );

  }

  function padTypeScriptKey ( $key ) {

    return preg_match ( '/^[A-Za-z_$][A-Za-z0-9_$]*$/', $key ) ? $key : json_encode ( $key, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

  }

?>
