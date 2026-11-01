<?php

  // vimeo - A Vimeo video, played with dnt=1: Vimeo is asked not to track the session. The
  // addresses: vimeo.com/<id>, vimeo.com/channels/<name>/<id>, vimeo.com/groups/<name>/
  // videos/<id>, vimeo.com/video/<id> and player.vimeo.com/video/<id>; named, a bare id of
  // four to twelve digits as well, {vimeo '76979871'}. A #t= in the address, or at=, is the
  // moment to start at. Its thumbnail has no address of its own, so the poster is drawn
  // unless poster= gives one.

  return [

    'name'    => 'Vimeo',
    'kind'    => 'video',
    'frame'   => 'https://player.vimeo.com',
    'options' => [ 'at' ],

    'match'   => function ( $source, $named ) {

      if ( $named and preg_match ( '/^\d{4,12}$/D', $source ) )
        return [ 'id' => $source, 'start' => 0 ];

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      $start = padEmbedStart ( $url ['query'] ['t'] ?? $url ['query'] ['start'] ?? $url ['mark'] ['t'] ?? '' );

      if ( $url ['host'] == 'vimeo.com' and preg_match ( '#^/(?:(?:channels|groups)/[\w-]+/(?:videos/)?|video/)?(\d{4,12})/?$#D', $url ['path'], $m ) )
        return [ 'id' => $m [1], 'start' => $start ];

      if ( $url ['host'] == 'player.vimeo.com' and preg_match ( '#^/video/(\d{4,12})/?$#D', $url ['path'], $m ) )
        return [ 'id' => $m [1], 'start' => $start ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      $id    = $parts ['id'];
      $start = padEmbedStart ( $opts ['at'] ?? '' ) ?: $parts ['start'];

      return [ 'src'   => "https://player.vimeo.com/video/$id?autoplay=1&dnt=1" . ( $start ? "#t={$start}s" : '' ),
               'link'  => "https://vimeo.com/$id" . ( $start ? "#t={$start}s" : '' ),
               'allow' => 'autoplay; encrypted-media; picture-in-picture; fullscreen' ];

    },

  ];

?>
