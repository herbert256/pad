<?php

  // Captures the local variables of the PHP function that is driving this PAD run, so they
  // can be resolved as fields.
  //
  // Included from level/level.php for every tag while $padLvlFun marks the enclosing level
  // as being inside such a call (set by start/pad/function.php, i.e. padCode/padSandbox/
  // padStrFun). An include shares its caller's scope, so get_defined_vars() here sees that
  // function's locals; the storable ones that are not already globals, current-row fields,
  // parameters, options or level sets are copied into $padLvlFunVar [$pad], where
  // lib/field/level.php and the at/ property lookups find them.

  $padLvlFunVar [$pad] = [];

  foreach ( get_defined_vars () as $padStrKey => $padStrVal )
    if ( ! isset ( $GLOBALS [$padStrKey] ) )
      if ( padValidStore ( $padStrKey ) )
        if ( ! isset ( $padCurrent [$pad] [$padStrKey] ) )
          if ( ! isset ( $padPrm [$pad] [$padStrKey] ) )
            if ( ! isset ( $padOpt [$pad] [$padStrKey] ) )
              if ( ! isset ( $padSetLvl [$pad] [$padStrKey] ) )
                $padLvlFunVar [$pad] [$padStrKey] = $padStrVal;

?>