<?php

  // semaphore - a text in flag semaphore: a figure per letter holding two flags, red and
  // yellow, at two of eight positions around the body, as the code has them - a digit after
  // the numeral sign as the letters A to K have it, the letters after digits behind J, the
  // letters sign - each figure with its letter under it. size= is the width of a figure in
  // pixels (64 unless given), title= the accessible name; the description says every sign.
  //
  //   {notation:semaphore 'PAD'/}
  //   {notation:semaphore 'Room 12', size=48/}

  list ( $padSemaphoreWords, $padSemaphoreUnknown ) = padSemaphorePairs ( $padNotationText );

  if ( $padSemaphoreUnknown and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . " has no semaphore for '" . padMakeSafe ( implode ( '', $padSemaphoreUnknown ), 20 )
               . "' - it signals the letters A to Z and the digits" );

  if ( ! $padSemaphoreWords )
    return '';

  $padSemaphoreSize  = padNotationInt ( 'size', 64, 16, 400 );
  $padSemaphoreTitle = padNotationOption ( 'title' );

  if ( $padSemaphoreTitle === '' )
    $padSemaphoreTitle = padMakeSafe ( $padNotationText, 200 ) . ' in flag semaphore';

  return padNotationWrap ( padNotationStyle ( 'semaphore', PAD_SEMAPHORE_COLOURS )
       . padSemaphoreSvg ( $padSemaphoreWords, $padSemaphoreSize,
                           padNotationId ( 'semaphore', [ $padNotationText, $padSemaphoreSize, $padSemaphoreTitle ] ), $padSemaphoreTitle ) );

?>
