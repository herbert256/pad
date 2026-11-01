<?php

  // The sounds: the files of subs/sounds/types/ (padKinds, lib/subsystem.php) - a sound is
  // added by adding its recipe there. The rest of the sounds is the subsystem in subs/sounds/,
  // its library subs/sounds/_lib/ loaded when a page plays one or asks for its WAV.
  //
  // padSoundKinds  the sounds there are
  // padSoundKind   whether a name is one
  // padSoundWav    the bytes of a sound as a WAV file - 16 bits, mono, 22050 a second -
  //                made from its recipe as the browser makes it, the same bytes every time:
  //                file_put_contents ( 'chime.wav', padSoundWav ( 'chime' ) )
  // padSoundSend   a sound as the whole answer of a page, from its PHP - as padUploadSend
  //                sends a file: padSoundSend ( 'chime' ), or with TRUE as a download

  function padSoundKinds () {

    return padKinds ( 'sounds' );

  }

  function padSoundKind ( $name ) {

    return padKind ( 'sounds', $name );

  }

  function padSoundWav ( $kind ) {

    include_once PAD . 'subs/sounds/_lib/sound.php';

    if ( ! padSoundKind ( $kind ) ) {
      padError ( "padSoundWav: there is no sound named '" . padMakeSafe ( is_scalar ( $kind ) ? (string) $kind : get_debug_type ( $kind ), 20 ) . "' - "
                 . implode ( ', ', padSoundKinds () ) );
      return '';
    }

    return padSoundWavBytes ( padSoundRecipe ( $kind ) );

  }

  function padSoundSend ( $kind, $download = FALSE ) {

    include_once PAD . 'subs/sounds/_lib/sound.php';

    if ( ! padSoundKind ( $kind ) )
      return padAbort ( 404 );

    return padSoundAnswer ( $kind, $download );

  }

?>
