<?php

  // dailymotion - A Dailymotion video: dailymotion.com/video/<id>, dai.ly/<id> and
  // dailymotion.com/embed/video/<id>; named, a bare id as well, {embed:dailymotion 'x8j4k2c'}.
  // at= is the moment to start at; a ?start= in the address says it too.

  return [

    'name'    => 'Dailymotion',
    'kind'    => 'video',
    'frame'   => 'https://www.dailymotion.com',
    'options' => [ 'at' ],

    'match'   => function ( $source, $named ) {

      if ( $named and preg_match ( '/^x[a-z0-9]{4,14}$/Di', $source ) )
        return [ 'id' => $source, 'start' => 0 ];

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      $start = padEmbedStart ( $url ['query'] ['start'] ?? '' );

      if ( $url ['host'] == 'dailymotion.com' and preg_match ( '#^/(?:embed/)?video/(x[a-z0-9]{4,14})/?$#Di', $url ['path'], $m ) )
        return [ 'id' => $m [1], 'start' => $start ];

      if ( $url ['host'] == 'dai.ly' and preg_match ( '#^/(x[a-z0-9]{4,14})/?$#Di', $url ['path'], $m ) )
        return [ 'id' => $m [1], 'start' => $start ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      $start = padEmbedStart ( $opts ['at'] ?? '' ) ?: $parts ['start'];

      return [ 'src'   => "https://www.dailymotion.com/embed/video/{$parts ['id']}?autoplay=1" . ( $start ? "&start=$start" : '' ),
               'link'  => "https://www.dailymotion.com/video/{$parts ['id']}" . ( $start ? "?start=$start" : '' ),
               'allow' => 'autoplay; fullscreen; picture-in-picture' ];

    },

  ];

?>
