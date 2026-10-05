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
  // padTreeKids   the children of the current row of a tree level, [] when it has none

  function padTreeLevel ( $from ) {

    global $padLevelId, $padTreeStore;

    for ( $i = $from; $i > 0; $i-- )
      if ( isset ( $padTreeStore [ $padLevelId [$i] ?? 0 ] ) )
        return $i;

    return FALSE;

  }

  function padTreeKids ( $lvl ) {

    global $padCurrent, $padLevelId, $padTreeStore;

    $field = $padTreeStore [ $padLevelId [$lvl] ] ['children'];
    $kids  = $padCurrent [$lvl] [$field] ?? [];

    return ( is_array ( $kids ) ) ? $kids : [];

  }

?>
