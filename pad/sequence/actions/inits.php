<?php

  // Collects the actions to run into $pqActions, a list of [ name, raw parameter string ] in
  // the order written, a repeated action once per mention. It starts from the
  // $pqAction/$pqActionParm pair that
  // inits/find/ recognised in the tag name itself, then adds every tag option whose name
  // is a known action (a file in PA); the explicit action='name|parm' form takes its name
  // from the first '|' segment. Valueless options arrive as TRUE and become ''. The name
  // goes into an include path, so it must be a plain name before it is looked up - with
  // ../ in it, action= reached any .php on disk.

  // An option of the tag's own action is its parameter, not a second mention: {a:shift shift=3}
  // ran shift twice, the first time with a count of 1, so it showed 1 and took 1 out of the
  // store. The action keeps the first place, with the option's value.

  $pqActionFront = '';

  if ( $pqAction )  {

    if ( $pqActionParm === TRUE )
      $pqActionParm = '';

    if ( $pqActionParm === '' and $pqEntry == 'tag' )
      foreach ( $padParms [$pad] as $padV )
        if ( $padV ['padPrmKind'] == 'option' and $padV ['padPrmName'] == $pqAction )
          $pqActionFront = $pqAction;

    if ( ! $pqActionFront )
      $pqActions [] = [ $pqAction, $pqActionParm ];

  }

  // A tag's options only - see plays/inits.php: an expression's run took the surrounding
  // tag's actions as its own.

  foreach ( ( $pqEntry == 'tag' ) ? $padParms [$pad] : [] as $padV )

    if ( $padV ['padPrmKind'] == 'option' ) {

      $pqActionParm = $padV ['padPrmValue'];

      // Only an option that can be an action is read as one: from=[1] and every other option
      // went through the explode below, and a list ended the request on its TypeError.

      if ( $padV ['padPrmName'] != 'action' and ! pqAction ( $padV ['padPrmName'] ) )
        continue;

      if ( $pqActionParm === TRUE )
        $pqActionParm = '';

      // A list given to an action - intersection=b, b a store written bare and so its values,
      // or a list of an expression - is acted with as a store is: it is kept under an
      // __action__ name for the run, as actions/set.php keeps the lists of an expression's
      // action, and actions/actions.php drops it afterwards.

      if ( is_array ( $pqActionParm ) and $padV ['padPrmName'] != 'action' ) {
        $pqActionParm = pqActionList ( $padV ['padPrmName'], $pqActionParm );
        if ( $pqActionParm === NULL )
          continue;
      }

      if ( ! is_scalar ( $pqActionParm ) )
        continue;

      $pqActionList = padExplode ( $pqActionParm, '|' );

      $pqAction = ( $padV ['padPrmName'] == 'action' ) ? array_shift ( $pqActionList ) : $padV ['padPrmName'];

      if ( padValidName ( $pqAction ) and pqAction ( $pqAction ) and $pqAction === $pqActionFront ) {
        array_unshift ( $pqActions, [ $pqAction, implode ( '|', $pqActionList ) ] );
        $pqActionFront = '';
      }

      elseif ( padValidName ( $pqAction ) and pqAction ( $pqAction ) )
        $pqActions [] = [ $pqAction, implode ( '|', $pqActionList ) ];

      // A name action= gives has to be an action, as make='x' has to be a type: action='nope'
      // and action='../x' were dropped without a word.

      elseif ( $padV ['padPrmName'] == 'action' and $padV ['padPrmValue'] !== TRUE )
        padError ( "action='" . ( is_scalar ( $pqAction ) ? $pqAction : '' ) . "' is not a sequence action" );

    }

?>
