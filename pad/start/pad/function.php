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

  // The build and isolation flags are this pass's arguments, and the import below must not
  // replace them: once any earlier pass has left them behind as globals, `global` rebinds
  // the four names to those stale values - which is how a {code sandbox} after a padCode()
  // ran with the padCode's flags and leaked what it should have unset. The source already
  // has the same protection through the unset of padStrCod above. The build kind comes in
  // the same way - a padStrFun() parameter, or set by padCode()/padSandbox() - where the
  // function path used to read it from whatever the last pass left in the global.

  $padStrKeep = [ $padStrBld, $padStrBox, $padStrCln, $padStrRes ];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    global $$padStrKey;

  list ( $padStrBld, $padStrBox, $padStrCln, $padStrRes ) = $padStrKeep;

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