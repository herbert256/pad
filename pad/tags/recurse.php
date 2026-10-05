<?php

  // {recurse} inside a {tree}: renders the tree's body once more, for the children of the
  // current row - each child a row of its own, with {branch} and {recurse} working again
  // one level down. A row without children renders nothing here.
  //
  // The new level is named as the tree is, so depth@tree, first@tree and count@tree speak of
  // the list being rendered, and it carries the tree's sort, where and reverse along, so the
  // children are ordered and filtered as their parents were. See lib/tree.php.

  $padTreeLvl = padTreeLevel ( $pad - 1 );

  if ( $padTreeLvl === FALSE ) {

    if ( $padCheckSyntax )
      padError ( "a {recurse} belongs inside a {tree}" );

    return FALSE;

  }

  $padTreeUp = $padTreeStore [ $padLevelId [$padTreeLvl] ];

  $padTreeStore [ $padLevelId [$pad] ] = [
    'body'     => $padTreeUp ['body'],
    'children' => $padTreeUp ['children'],
    'depth'    => $padTreeUp ['depth'] + 1
  ];

  foreach ( $padParms [$padTreeLvl] as $padTreeOne )
    if ( $padTreeOne ['padPrmKind'] == 'option' and in_array ( $padTreeOne ['padPrmName'], [ 'sort', 'where', 'reverse' ] ) ) {
      $padParms [$pad] [] = $padTreeOne;
      $padPrm   [$pad] [ $padTreeOne ['padPrmName'] ] = $padPrm [$padTreeLvl] [ $padTreeOne ['padPrmName'] ];
    }

  $padForceTagName = $padName [$padTreeLvl];
  $padContent      = $padTreeUp ['body'];

  return padTreeKids ( $padTreeLvl );

?>
