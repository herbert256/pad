<?php

  // Recursive trees: {tree 'menu', children='items'} renders its body for every row, and
  // inside it {recurse} renders the same body again for the current row's children, {branch}
  // only when there are children - menus, category trees, threaded comments and {files
  // recursive} results, where a tag could call itself but nothing said "render this body
  // again for the children".
  //
  // $padTreeStore holds, per level id, what a {tree} level and each {recurse} level below it
  // know: the body to render, the field holding the children, and the depth - 1 for the
  // tree's own rows, one more per {recurse}. Level ids are unique within a request, so a
  // slot reused by a later level never meets an old entry.
  //
  // padTreeLevel  the nearest enclosing level that is a tree or a recurse, FALSE outside one
  //               - so a {branch} or {recurse} inside an {if} still finds its rows
  // padTreeRows   the rows a {tree} names: a {data} block, a stored sequence, an array of the
  //               page or of an enclosing row, or a _data/ file - in that order; NULL when
  //               there is nothing by that name
  // padTreeKids   the children of the current row of a tree level, [] when it has none

  function padTreeLevel ( $from ) {

    global $padLevelId, $padTreeStore;

    for ( $i = $from; $i > 0; $i-- )
      if ( isset ( $padTreeStore [ $padLevelId [$i] ?? 0 ] ) )
        return $i;

    return FALSE;

  }

  function padTreeRows ( $name ) {

    global $padDataStore, $pqStore;

    if ( ! is_string ( $name ) or $name === '' )
      return NULL;

    if ( isset ( $padDataStore [$name] ) ) return $padDataStore [$name];
    if ( isset ( $pqStore      [$name] ) ) return $pqStore      [$name];

    if ( padValidName ( $name ) and padArrayCheck ( $name ) )
      return padArrayValue ( $name );

    $file = padValidName ( $name ) ? padDataFileName ( $name ) : FALSE;

    if ( $file )
      return padDataFileData ( $file );

    return NULL;

  }

  function padTreeKids ( $lvl ) {

    global $padCurrent, $padLevelId, $padTreeStore;

    $field = $padTreeStore [ $padLevelId [$lvl] ] ['children'];
    $kids  = $padCurrent [$lvl] [$field] ?? [];

    return ( is_array ( $kids ) ) ? $kids : [];

  }

?>
