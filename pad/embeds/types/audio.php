<?php

  // audio - A sound file - .mp3, .m4a, .aac, .ogg, .oga, .opus, .wav or .flac - of
  // www/<application>/, versioned as an {asset}, or of the web, as an <audio controls
  // preload="none"> with the title under it: nothing is loaded before it is played.

  return [

    'name'    => 'a sound file',
    'kind'    => 'audio',
    'generic' => TRUE,

    'match'   => fn ( $source, $named ) => padEmbedFile ( $source, [ 'mp3', 'm4a', 'aac', 'ogg', 'oga', 'opus', 'wav', 'flac' ] ),

    'embed'   => function ( $parts, $opts ) {

      $types = [ 'mp3' => 'audio/mpeg', 'm4a' => 'audio/mp4', 'aac' => 'audio/aac', 'ogg' => 'audio/ogg', 'oga' => 'audio/ogg',
                 'opus' => 'audio/ogg', 'wav' => 'audio/wav', 'flac' => 'audio/flac' ];

      return [ 'mode' => 'audio',
               'src'  => $parts ['local'] ? padAsset ( $parts ['url'] ) : $parts ['url'],
               'type' => $types [ $parts ['ext'] ] ];

    },

  ];

?>
