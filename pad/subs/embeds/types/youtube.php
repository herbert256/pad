<?php

  // youtube - A YouTube video, played from youtube-nocookie.com: no cookie is set before it
  // plays. The addresses: youtube.com/watch?v=, youtu.be/, /shorts/ (upright, 9:16),
  // /embed/, /live/ and /v/, and youtube-nocookie.com/embed/ - an id of eleven; named, a bare
  // id as well, {youtube 'aqz-KE-bpKQ'}. A t= or start= in the address, or a #t=, is the
  // moment to start at; at= says it too. With thumbnail the poster is the video's own picture
  // on i.ytimg.com - an image of Google's server, asked before the click.

  return [

    'name'    => 'YouTube',
    'kind'    => 'video',
    'frame'   => 'https://www.youtube-nocookie.com',
    'options' => [ 'at' ],

    'match'   => function ( $source, $named ) {

      if ( $named and preg_match ( '/^[A-Za-z0-9_-]{11}$/D', $source ) )
        return [ 'id' => $source, 'start' => 0, 'short' => FALSE ];

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      $start = padEmbedStart ( $url ['query'] ['t'] ?? $url ['query'] ['start'] ?? $url ['mark'] ['t'] ?? '' );

      if ( in_array ( $url ['host'], [ 'youtube.com', 'youtube-nocookie.com', 'music.youtube.com' ], TRUE ) ) {

        if ( $url ['path'] == '/watch' and is_string ( $url ['query'] ['v'] ?? NULL ) and preg_match ( '/^[A-Za-z0-9_-]{11}$/D', $url ['query'] ['v'] ) )
          return [ 'id' => $url ['query'] ['v'], 'start' => $start, 'short' => FALSE ];

        if ( preg_match ( '#^/(embed|shorts|live|v)/([A-Za-z0-9_-]{11})/?$#D', $url ['path'], $m ) )
          return [ 'id' => $m [2], 'start' => $start, 'short' => $m [1] == 'shorts' ];

      }

      if ( $url ['host'] == 'youtu.be' and preg_match ( '#^/([A-Za-z0-9_-]{11})/?$#D', $url ['path'], $m ) )
        return [ 'id' => $m [1], 'start' => $start, 'short' => FALSE ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      $id    = $parts ['id'];
      $start = padEmbedStart ( $opts ['at'] ?? '' ) ?: $parts ['start'];

      return [ 'src'   => "https://www.youtube-nocookie.com/embed/$id?autoplay=1&rel=0" . ( $start ? "&start=$start" : '' ),
               'link'  => "https://www.youtube.com/watch?v=$id" . ( $start ? "&t={$start}s" : '' ),
               'thumb' => "https://i.ytimg.com/vi/$id/hqdefault.jpg",
               'ratio' => $parts ['short'] ? '9:16' : '16:9',
               'allow' => 'autoplay; encrypted-media; picture-in-picture; fullscreen' ];

    },

  ];

?>
