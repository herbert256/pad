<?php

  // The WAV link of a {sound:chime} on a page that flushes early answers the WAV: asked with
  // &padSound the page flushes nothing, as it flushes nothing asked with &padPdf - the head
  // of the page went out in front, text/html, and the sound's answer was an error in it.

  $wav    = padCurl ( $padHost . 'regression/pages/?flush/sound_inner&padInclude&padSound=chime' );
  $same   = str_replace ( "\r\n", "\n", trim ( padSoundWav ( 'chime' ) ) );

  $answer = ( $wav ['data'] === $same ? 'the WAV of chime' : 'another answer' ) . ', ' . ( $wav ['headers'] ['Content-Type'] ?? '?' );

?>
