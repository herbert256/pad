<?php

  // Every sound of pad/subs/sounds/types/ as padSoundWav makes it: a WAV of 16-bit mono PCM at
  // 22050 samples a second, as long as its recipe, its loudest sample at the recipe's level
  // (0.8 of full scale, 26214), no sample silent at the very start - the attack starts from
  // nothing - and the md5 of the file, the same bytes on every request.

  $soundRows = [];

  foreach ( padSoundKinds () as $soundKind ) {

    $soundWav  = padSoundWav ( $soundKind );
    $soundHead = unpack ( 'a4riff/Vsize/a4wave/a4fmt/Vchunk/vformat/vchannels/Vrate/Vbytes/valign/vbits/a4data/Vlength', $soundWav );
    $soundPcm  = array_values ( unpack ( 's*', substr ( $soundWav, 44 ) ) );
    $soundPeak = max ( array_map ( 'abs', $soundPcm ) );

    $soundRows [] = $soundKind . ' ' . $soundHead ['riff'] . $soundHead ['wave'] . ' pcm' . $soundHead ['format']
                  . ' ' . $soundHead ['channels'] . 'ch ' . $soundHead ['rate'] . 'Hz ' . $soundHead ['bits'] . 'bit '
                  . count ( $soundPcm ) . ' samples ' . round ( count ( $soundPcm ) / 22050, 3 ) . 's peak ' . $soundPeak
                  . ' first ' . $soundPcm [0] . ' ' . md5 ( $soundWav );

  }

  $soundList = implode ( ' // ', $soundRows );

?>
