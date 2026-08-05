<?php

  // Runs a nested pass inside a real PHP function body, so that it gets a variable scope of
  // its own rather than writing straight into $GLOBALS. Always included from padStrFun() in
  // lib/execute.php, which {code function=...} reaches through start/pad/parms.php and
  // padCode()/padSandbox() through start/function.php.
  //
  // The whole of $GLOBALS is imported into that scope so the pass still sees engine state;
  // the global padStrCod is dropped first, because importing it would overwrite the argument
  // carrying the source. $padLvlFun[$pad] marks the level the pass opens under as a function
  // level, which is what makes level/level.php run level/function.php and expose these locals
  // to tags as fields. On the way out - unless the pass was sandboxed or cleaned, in which
  // case nothing should escape - application variables the pass created are copied to
  // $GLOBALS so they outlive the function scope.

  if ( isset ( $GLOBALS ['padStrCod'] ) )
    unset ( $GLOBALS ['padStrCod'] );

  $GLOBALS ['padStrFun'] = TRUE;
  $GLOBALS ['padStrFunCnt']++;
  $GLOBALS ['padStrFunVar'] [ $GLOBALS ['padStrFunCnt'] ] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    global $$padStrKey;

  $padLvlFun [$pad] = $padStrFunCnt;
  $padStrFunResult = include PAD . 'start/pad/pad.php';

  if ( ! $padStrBox and ! $padStrCln )
    foreach ( get_defined_vars () as $padStrKey => $padStrVal )
      if ( padValidStore ( $padStrKey ) )
        if ( ! isset ( $GLOBALS [$padStrKey] ) )
          $GLOBALS [$padStrKey] = $padStrVal;

  $padStrFunCnt--;

  return $padStrFunResult;

?>