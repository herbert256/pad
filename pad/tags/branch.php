<?php

  // {branch} ... {/branch} inside a {tree}: renders its content only when the current row
  // of the tree has children - the <ul> around a {recurse} - and its @else@ part for a
  // leaf. See lib/tree.php.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {branch} never closes" );

  $padTreeLvl = padTreeLevel ( $pad - 1 );

  if ( $padTreeLvl === FALSE ) {

    if ( $padCheckSyntax )
      padError ( "a {branch} belongs inside a {tree}" );

    return FALSE;

  }

  return count ( padTreeKids ( $padTreeLvl ) ) > 0;

?>
