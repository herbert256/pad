<?php

  // A {cache} section that holds a {sound} with a link to its WAV is not kept: the page asked
  // again with &padSound answers the WAV the second time too. Kept, the section came back on
  // the second request without the tag having run, and the link answered the page's HTML -
  // its links carry the request's own values as well, which a kept copy gives every visitor.

  $soundAsk   = $padHost . 'regression/framework/?sounds/a_sound_in_a_cache_section_keeps_its_wav_link_alive';
  $soundFirst = padCurl ( $soundAsk );
  $soundWav   = padCurl ( $soundAsk . '&padSound=chime' );

  $soundAnswer = ( str_contains ( $soundFirst ['data'], 'class="pad-sound-download"' ) ? 'the page' : 'something else' )
               . ', then ' . ( $soundWav ['headers'] ['Content-Type'] ?? '?' );

?>
