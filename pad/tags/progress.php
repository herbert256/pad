<?php

  // {progress 72} - a progress bar, lib/progress.php: the native <progress> element styled,
  // under a line with the label and the percentage. The first parameter is the value; max=
  // is what it counts to (default 100), label= names it - shown, and the accessible name -
  // color= is accent (default), success, warning, danger or neutral, and size= small,
  // medium (default) or large. steps draws a row of dots instead, one per step up to max,
  // the steps up to the value filled: a checkout or a wizard.
  //
  //   {progress 72, max=100, label='Upload'}
  //   {progress 3, max=5, steps, label='Checkout'}

  $padProgressValue = trim ( (string) $padParm );
  $padProgressMax   = padTagParm ( 'max', 100 );
  $padProgressColor = strtolower ( trim ( (string) padTagParm ( 'color', 'accent' ) ) );
  $padProgressSize  = strtolower ( trim ( (string) padTagParm ( 'size', 'medium' ) ) );
  $padProgressSteps = (bool) padTagParm ( 'steps', FALSE );

  if ( ! is_numeric ( $padProgressValue ) ) {
    if ( $padCheckSyntax )
      padError ( "the progress has no number for its value: '" . padMakeSafe ( $padProgressValue, 20 ) . "' - {progress 72}" );
    $padProgressValue = 0;
  }

  if ( ! is_numeric ( $padProgressMax ) or $padProgressMax <= 0 or ( $padProgressSteps and ( $padProgressMax != (int) $padProgressMax or $padProgressMax > 50 ) ) ) {
    if ( $padCheckSyntax )
      padError ( "the progress has no usable max '" . padMakeSafe ( $padProgressMax, 20 ) . "' - a number above 0" . ( $padProgressSteps ? ', for steps a whole number up to 50' : '' ) );
    $padProgressMax = $padProgressSteps ? 5 : 100;
  }

  if ( ! in_array ( $padProgressColor, PAD_PROGRESS_COLORS ) ) {
    if ( $padCheckSyntax )
      padError ( "the progress has no color '" . padMakeSafe ( $padProgressColor, 20 ) . "' - " . implode ( ', ', PAD_PROGRESS_COLORS ) );
    $padProgressColor = 'accent';
  }

  if ( ! in_array ( $padProgressSize, PAD_PROGRESS_SIZES ) ) {
    if ( $padCheckSyntax )
      padError ( "the progress has no size '" . padMakeSafe ( $padProgressSize, 20 ) . "' - " . implode ( ', ', PAD_PROGRESS_SIZES ) );
    $padProgressSize = 'medium';
  }

  if ( $padProgressSteps )
    return padProgressSteps ( $padProgressValue, $padProgressMax, (string) padTagParm ( 'label' ), $padProgressColor, $padProgressSize );

  return padProgress ( $padProgressValue, $padProgressMax, (string) padTagParm ( 'label' ), $padProgressColor, $padProgressSize );

?>
