<?php

  // videofile - A video file - .mp4, .m4v, .webm, .ogv or .mov - of www/<application>/,
  // versioned as an {asset}, or of the web, as a <video controls preload="none">: nothing is
  // loaded before it is played. poster= is a picture of www/<application>/. The {video} tag
  // writes a file of the application the same way, in its own markup.

  return [

    'name'    => 'a video file',
    'kind'    => 'video',
    'generic' => TRUE,

    'match'   => fn ( $source, $named ) => padEmbedFile ( $source, [ 'mp4', 'm4v', 'webm', 'ogv', 'mov' ] ),

    'embed'   => function ( $parts, $opts ) {

      $types = [ 'mp4' => 'video/mp4', 'm4v' => 'video/mp4', 'webm' => 'video/webm', 'ogv' => 'video/ogg', 'mov' => 'video/quicktime' ];

      return [ 'mode' => 'video',
               'src'  => $parts ['local'] ? padAsset ( $parts ['url'] ) : $parts ['url'],
               'type' => $types [ $parts ['ext'] ] ];

    },

  ];

?>
