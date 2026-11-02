<?php

  // {pad:branch} ... {/pad:branch} inside a {pad:tree}: renders its content only when the current row
  // of the tree has children - the <ul> around a {pad:recurse} - and its @else@ part for a
  // leaf: children the tree's where= leaves out count for none. See tags/_lib/tree.php.

  include_once PAD . 'tags/_lib/tree.php';

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {pad:branch} never closes" );

  $padTreeLvl = padTreeLevel ( $pad - 1 );

  if ( $padTreeLvl === FALSE ) {

    if ( $padCheckSyntax )
      padError ( "a {pad:branch} belongs inside a {pad:tree}" );

    return FALSE;

  }

  $padTreeKidsNow = padTreeKids ( $padTreeLvl );

  // The tree's where= filters the children too - {pad:recurse} carries it onto their level - so a
  // row has children here only when one of them passes it: a node whose children the where
  // left out entirely wrote its <ul>{pad:recurse}</ul> as an empty <ul></ul> and never took the
  // branch's @else@. Each child is asked as handling/types/where.php asks a row: standing as
  // the current row, with its key and its place, its fields before every other name.

  if ( count ( $padTreeKidsNow ) and is_string ( $padPrm [$padTreeLvl] ['where'] ?? NULL ) ) {

    $padTreeKeep = [ $padCurrent [$pad], $padKey [$pad], $padOccur [$pad] ];
    $padTreePass = FALSE;
    $padTreeNth  = 0;

    foreach ( $padTreeKidsNow as $padTreeKey => $padTreeKid ) {

      if ( is_object ( $padTreeKid ) )
        $padTreeKid = padDataObject ( $padTreeKid );

      $padCurrent [$pad] = is_array ( $padTreeKid ) ? $padTreeKid : [];
      $padKey     [$pad] = $padTreeKey;
      $padOccur   [$pad] = ++$padTreeNth;

      if ( padEvalBool ( $padPrm [$padTreeLvl] ['where'] ) ) {
        $padTreePass = TRUE;
        break;
      }

    }

    list ( $padCurrent [$pad], $padKey [$pad], $padOccur [$pad] ) = $padTreeKeep;

    return $padTreePass;

  }

  return count ( $padTreeKidsNow ) > 0;

?>
