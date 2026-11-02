<?php

  // The shared body of sound:kind tags, such as {sound:chime}. The caller sets
  // $padSoundKind; registered kinds are files in subs/sounds/types/.
  //
  // The page asked again with &padSound=<kind> - the download link and the <audio> that
  // stands in for the button without script ask for it - answers the sound's WAV file in
  // place of the page, as the first {sound:chime} of that kind on it renders (padSoundAnswer).

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
