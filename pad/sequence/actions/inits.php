<?php

  // Collects the actions to run into $pqActions, keyed by action name with the raw
  // parameter string as the value. It starts from the $pqAction/$pqActionParm pair that
  // inits/find/ recognised in the tag name itself, then adds every tag option whose name
  // is a known action (a file in PA); the explicit action='name|parm' form takes its name
  // from the first '|' segment. Valueless options arrive as TRUE and become ''. The name
  // goes into an include path, so it must be a plain name before it is looked up - with
  // ../ in it, action= reached any .php on disk.

  if ( $pqAction )  {
    if ( $pqActionParm === TRUE )
      $pqActionParm = '';
    $pqActions [$pqAction] = $pqActionParm;
  }

  // A tag's options only - see plays/inits.php: an expression's run took the surrounding
  // tag's actions as its own.

  foreach ( ( $pqEntry == 'tag' ) ? $padParms [$pad] : [] as $padV )

    if ( $padV ['padPrmKind'] == 'option' ) {

      $pqActionParm = $padV ['padPrmValue'];

      if ( $pqActionParm === TRUE )
        $pqActionParm = '';

      $pqActionList = padExplode ( $pqActionParm, '|' );

      $pqAction = ( $padV ['padPrmName'] == 'action' ) ? array_shift ( $pqActionList ) : $padV ['padPrmName'];

      if ( padValidName ( $pqAction ) and pqAction ( $pqAction ) )
        $pqActions [$pqAction] = implode ( '|', $pqActionList );

    }

?>
