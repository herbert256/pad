<?php

  // stopwatch - An elapsed-time display with independent Start, Pause and Reset controls.
  // The first parameter is elapsed seconds, default zero. autostart starts on page load.
  // label= names the timer; controls work with a keyboard and never submit a parent form.
  $padTimeElapsed = $padParm === NULL || $padParm === '' ? 0 : $padParm;
  if ( ! is_numeric ( $padTimeElapsed ) || ! is_finite ( (float) $padTimeElapsed ) || $padTimeElapsed < 0 || $padTimeElapsed > 315360000 ) {
    if ( $padCheckSyntax )
      padError ( 'the stopwatch elapsed seconds must be a number between 0 and 315360000' );
    return FALSE;
  }
  return padTimeStopwatch ( (float) $padTimeElapsed, (bool) padTagParm ( 'autostart', FALSE ),
                            padParmText ( padTagParm ( 'label', 'Stopwatch' ) ) );

?>
