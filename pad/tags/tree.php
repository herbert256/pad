<?php

  // {pad:tree 'menu', children='items'} ... {/pad:tree}: renders its body for every row of a tree,
  // and inside the body {pad:recurse} renders the same body again for the current row's
  // children - {pad:branch} ... {/pad:branch} only when the row has any. depth@tree is 1 for these
  // rows and one more per {pad:recurse}. See tags/_lib/tree.php.
  //
  // The rows are named by the first parameter or by data= - a {format:auto} block, a stored
  // sequence, an array of the page or an enclosing row, a _data/ file (padNamedRows in
  // functions/_lib/lookup.php); children names the
  // field that holds a row's children, 'children' when not given. The body is the content
  // as written - without the @else@ part, which shows when there are no rows at all.

  $padTreeName = ( $padParm !== '' ) ? $padParm : padTagParm ( 'data' );
  $padTreeKids = (string) padTagParm ( 'children', 'children' );

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {pad:tree} never closes" );

  if ( $padTreeName === '' or $padTreeName === TRUE ) {

    if ( $padCheckSyntax )
      padError ( "the {pad:tree} needs its rows - {pad:tree 'menu'} or data='menu'" );

    return FALSE;

  }

  include_once PAD . 'functions/_lib/lookup.php';

  $padTreeRows = padNamedRows ( $padTreeName );

  if ( $padTreeRows === NULL ) {

    if ( $padCheckSyntax and ! padStrHidden ( $padTreeName ) )
      padError ( "there are no rows named '" . padMakeSafe ( (string) $padTreeName, 40 ) . "' for the {pad:tree}" );

    return FALSE;

  }

  $padTreeStore [ $padLevelId [$pad] ] = [
    'body'     => $padContent,
    'children' => $padTreeKids,
    'depth'    => 1
  ];

  return $padTreeRows;

?>
