<?php

  if ( $pqAction )  {
    if ( $pqActionParm === TRUE )
      $pqActionParm = '';
    $pqActions [$pqAction] = $pqActionParm;
  }

  foreach ( $padParms [$pad] as $padV )

    if ( $padV ['padPrmKind'] == 'option' ) {

      $pqActionParm = $padV ['padPrmValue'];

      if ( $pqActionParm === TRUE )
        $pqActionParm = '';

      $pqActionList = padExplode ( $pqActionParm, '|' );

      $pqAction = ( $padV ['padPrmName'] == 'action' ) ? array_shift ( $pqActionList ) : $padV ['padPrmName'];

      if ( pqAction ( $pqAction ) )
        $pqActions [$pqAction] = implode ( '|', $pqActionList );

    }

?>
