<?php

  // Collects the actions to run into $pqActions, keyed by action name with the raw
  // parameter string as the value. It starts from the $pqAction/$pqActionParm pair that
  // inits/find/ recognised in the tag name itself, then adds every tag option whose name
  // is a known action (a file in PA); the explicit action='name|parm' form takes its name
  // from the first '|' segment. Valueless options arrive as TRUE and become ''.

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