<?php

  // The page asked again with &padSound=<kind> - what the download link and the link that
  // stands in for the button without script ask for - answers the WAV of that sound in place
  // of the page: the bytes of padSoundWav, as audio/wav. A kind the page plays no {sound:chime} of
  // is no answer of it: the page itself comes back.

  $soundAsk  = $padHost . 'regression/framework/?sounds/download_links_the_page_asked_for_the_wav&padSound=';
  $soundWav  = padCurl ( $soundAsk . 'chime' );
  $soundPage = padCurl ( $soundAsk . 'bell' );

  // padCurl reads a body as text - trimmed, its CR LF made LF - and the WAV is held to that.

  $soundSame   = str_replace ( "\r\n", "\n", trim ( padSoundWav ( 'chime' ) ) );

  $soundAnswer = ( $soundWav ['data'] === $soundSame ? 'the WAV of chime' : 'another answer' )
               . ', ' . ( $soundWav ['headers'] ['Content-Type'] ?? '?' ) . ', ' . ( $soundWav ['headers'] ['Content-Length'] ?? '?' ) . ' bytes // bell: '
               . ( str_contains ( $soundPage ['data'], 'class="pad-sound-download"' ) ? 'the page' : 'something else' );

?>
