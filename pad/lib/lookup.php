<?php

  // Rows by name, and the lookup pipe function that finds one of them by a key.
  //
  // padNamedRows  the rows a name stands for: a {data} block, a stored sequence, an array of
  //               the page or of an enclosing row, or a _data/ file - in that order; NULL
  //               when nothing holds that name. {tree 'menu'} and lookup('customers')
  //               both read their rows through it. With $keep a _data/ file is read once per
  //               request: a lookup asks for its set on every row it is piped on.
  // padLookup     the value of $field in the first row of $set whose $key equals $value -
  //               {echo $customer_id | lookup('customers', 'id', 'name')} - '' when no row
  //               matches. Without a field it is the row's first field other than the key.
  //               The set is indexed on the key once and the index kept for the request;
  //               it is built again only when the set itself changed - an unchanged array
  //               is the same array, so telling the two apart costs nothing.
  //
  // Joins existed for database tables through Select relations, and {at "id=5@customers"}
  // found one row, but two lists from different sources - JSON and CSV, an API and page
  // PHP - had no join in the template.

  function padNamedRows ( $name, $keep = FALSE ) {

    global $padDataStore, $pqStore, $padNamedFiles;

    if ( ! is_string ( $name ) or $name === '' )
      return NULL;

    if ( isset ( $padDataStore [$name] ) ) return $padDataStore [$name];
    if ( isset ( $pqStore      [$name] ) ) return $pqStore      [$name];

    if ( padValidName ( $name ) and padArrayCheck ( $name ) )
      return padArrayValue ( $name );

    $file = padValidName ( $name ) ? padDataFileName ( $name ) : FALSE;

    if ( ! $file )
      return NULL;

    if ( ! $keep )
      return padDataFileData ( $file );

    if ( ! isset ( $padNamedFiles [$file] ) )
      $padNamedFiles [$file] = padDataFileData ( $file );

    return $padNamedFiles [$file];

  }

  function padLookup ( $value, $set, $key, $field ) {

    global $padLookupIndex, $padCheckSyntax;

    $rows = padNamedRows ( $set, TRUE );

    if ( ! is_array ( $rows ) ) {

      if ( $padCheckSyntax and ! padStrHidden ( $set ) )
        padError ( "lookup has no data named '" . padMakeSafe ( (string) $set, 40 ) . "' to look in" );

      return '';

    }

    $id = "$set\0$key";

    if ( ! isset ( $padLookupIndex [$id] ) or $padLookupIndex [$id] ['rows'] !== $rows ) {

      $index = [];

      foreach ( $rows as $row )
        if ( is_array ( $row ) and isset ( $row [$key] ) and is_scalar ( $row [$key] ) )
          $index [ (string) $row [$key] ] ??= $row;

      $padLookupIndex [$id] = [ 'rows' => $rows, 'index' => $index ];

    }

    if ( ! is_scalar ( $value ) )
      return '';

    $row = $padLookupIndex [$id] ['index'] [ (string) $value ] ?? NULL;

    if ( $row === NULL )
      return '';

    if ( $field === '' ) {
      foreach ( $row as $name => $one )
        if ( (string) $name !== (string) $key )
          return is_scalar ( $one ) ? $one : '';
      return '';
    }

    $one = $row [$field] ?? '';

    return is_scalar ( $one ) ? $one : '';

  }

?>
