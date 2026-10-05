<?php

  // {tree 'menu', children='items'} ... {/tree}: renders its body for every row of a tree,
  // and inside the body {recurse} renders the same body again for the current row's
  // children - {branch} ... {/branch} only when the row has any. depth@tree is 1 for these
  // rows and one more per {recurse}. See lib/tree.php.
  //
  // The rows are named by the first parameter or by data= - a {data} block, a stored
  // sequence, an array of the page or an enclosing row, a _data/ file; children names the
  // field that holds a row's children, 'children' when not given. The body is the content
  // as written - without the @else@ part, which shows when there are no rows at all.

  $padTreeName = ( $padParm !== '' ) ? $padParm : padTagParm ( 'data' );
  $padTreeKids = (string) padTagParm ( 'children', 'children' );

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {tree} never closes" );

  if ( $padTreeName === '' or $padTreeName === TRUE ) {

    if ( $padCheckSyntax )
      padError ( "the {tree} needs its rows - {tree 'menu'} or data='menu'" );

    return FALSE;

  }

  $padTreeRows = padTreeRows ( $padTreeName );

  if ( $padTreeRows === NULL ) {

    if ( $padCheckSyntax )
      padError ( "there are no rows named '" . padMakeSafe ( (string) $padTreeName, 40 ) . "' for the {tree}" );

    return FALSE;

  }

  $padTreeStore [ $padLevelId [$pad] ] = [
    'body'     => $padContent,
    'children' => $padTreeKids,
    'depth'    => 1
  ];

  return $padTreeRows;

?>
