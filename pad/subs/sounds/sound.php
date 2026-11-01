<?php

  // The body of a sound, whichever way it is written: {sound 'chime'/}, or the sound: type -
  // {sound:chime/}. The caller sets $padSoundKind; a sound is a file of subs/sounds/types/
  // that returns its recipe, and the files there are all the sounds there are (padSoundKinds,
  // lib/sound.php). A sound is never reached bare: click, error and pop are the words of
  // everything else.
  //
  // The page asked again with &padSound=<kind> - the download link and the <audio> that
  // stands in for the button without script ask for it - answers the sound's WAV file in
  // place of the page, as the first {sound} of that kind on it renders (padSoundAnswer).

  include_once PAD . 'subs/sounds/_lib/sound.php';

  if ( ! padSoundKind ( $padSoundKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no sound named '" . padMakeSafe ( $padSoundKind, 20 ) . "' - " . implode ( ', ', padSoundKinds () ) );
    return '';
  }

  // The sound played goes on the xref record, which the reference application shows: the
  // pages that play each sound.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'sound', $padSoundKind );

  if ( ( $_GET ['padSound'] ?? NULL ) === $padSoundKind and ( $_SERVER ['REQUEST_METHOD'] ?? 'GET' ) == 'GET' )
    return padSoundAnswer ( $padSoundKind );

  // The markup is a value: a level loops over the data a data= option named, and would write
  // the sound once per row - so the level gets its one default occurrence back.

  $padSoundHtml = padSoundTag ( $padSoundKind );

  $padData [$pad] = padDefaultData ();

  return $padSoundHtml;

?>
