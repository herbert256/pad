<?php

  // Recursive trees: {pad:tree 'menu', children='items'} renders its body for every row, and
  // inside it {pad:recurse} renders the same body again for the current row's children, {pad:branch}
  // only when there are children - menus, category trees, threaded comments and {pad:files
  // recursive} results, where a tag could call itself but nothing said "render this body
  // again for the children".
  //
  // $padTreeStore holds, per level id, what a {pad:tree} level and each {pad:recurse} level below it
  // know: the body to render, the field holding the children, and the depth - 1 for the
  // tree's own rows, one more per {pad:recurse}. Level ids are unique within a request, so a
  // slot reused by a later level never meets an old entry.
  //
  // padTreeLevel  the nearest enclosing level that is a tree or a recurse, FALSE outside one
  //               - so a {pad:branch} or {pad:recurse} inside an {pad:if} still finds its rows
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

    // Children held in an object - an ArrayObject, a collection class, a generator - are the
    // list it holds, as a field asked for as a list reads them (padFieldListOf, lib/field/
    // level.php): the tree found none in them, so {pad:branch} was false and {pad:recurse} rendered
    // nothing, in silence, where a {children} tag over the same field iterated them. One that
    // runs once is read once, its array put in its place for the {pad:recurse} after the {pad:branch}.

    if ( is_object ( $kids ) ) {
      $list = padFieldListOf ( $kids, 3 );
      if ( padFieldOneShot ( $kids ) )
        $padCurrent [$lvl] [$field] = $list;
      $kids = $list;
    }

    return ( is_array ( $kids ) ) ? $kids : [];

  }

?>
