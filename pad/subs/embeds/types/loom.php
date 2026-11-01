<?php

  // loom - A recording of Loom - a screen and a face explaining something - in Loom's player:
  // loom.com/share/<id>, the title before the id or not, loom.com/embed/<id> and the old
  // useloom.com; named, a bare id as well, {loom 'e5b8c04bca094dd8a5507925ab887002'} - the
  // 32 hex digits of a video, nothing else. at= - or a ?t= in the address - is the moment to
  // start at, as Loom's own embed parameters say it (t=80s, t=1m20s). A recording is seen as
  // its owner shares it: one not open to anyone with the link asks the visitor to log in to
  // Loom, in the frame.

  return [

    'name'    => 'Loom',
    'kind'    => 'video',
    'frame'   => 'https://www.loom.com',
    'options' => [ 'at' ],

    'match'   => function ( $source, $named ) {

      if ( $named and preg_match ( '/^[0-9a-f]{32}$/D', $source ) )
        return [ 'id' => $source, 'start' => 0 ];

      if ( ! $url = padEmbedUrl ( $source ) or ! in_array ( $url ['host'], [ 'loom.com', 'useloom.com' ], TRUE ) )
        return NULL;

      if ( ! preg_match ( '#^/(?:share|embed)/(?:[A-Za-z0-9-]*-)?([0-9a-f]{32})/?$#D', $url ['path'], $m ) )
        return NULL;

      return [ 'id' => $m [1], 'start' => padEmbedStart ( $url ['query'] ['t'] ?? '' ) ];

    },

    'embed'   => function ( $parts, $opts ) {

      $start = padEmbedStart ( $opts ['at'] ?? '' ) ?: $parts ['start'];

      return [ 'src'   => "https://www.loom.com/embed/{$parts ['id']}?autoplay=1" . ( $start ? "&t={$start}s" : '' ),
               'link'  => "https://www.loom.com/share/{$parts ['id']}" . ( $start ? "?t={$start}s" : '' ),
               'allow' => 'autoplay; fullscreen; picture-in-picture' ];

    },

  ];

?>
