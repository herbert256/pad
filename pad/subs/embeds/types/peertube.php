<?php

  // peertube - A video of a PeerTube instance, the federated video platform without an
  // account or advertising: any host, known by the shape of its addresses - /w/<short id>,
  // /videos/watch/<uuid> and /videos/embed/<id>. Each instance is a site of its own, so its
  // origin is the frame-src a Content Security Policy needs. at= is the moment to start at;
  // a ?start= in the address says it too.

  return [

    'name'    => 'PeerTube',
    'kind'    => 'video',
    'frame'   => '',
    'generic' => TRUE,
    'options' => [ 'at' ],

    'match'   => function ( $source, $named ) {

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      if ( ! preg_match ( '#^/(?:w|videos/watch|videos/embed)/([1-9A-HJ-NP-Za-km-z]{22}|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/?$#D', $url ['path'], $m ) )
        return NULL;

      return [ 'origin' => strtolower ( parse_url ( $source, PHP_URL_SCHEME ) ) . '://' . strtolower ( parse_url ( $source, PHP_URL_HOST ) )
                           . ( parse_url ( $source, PHP_URL_PORT ) ? ':' . parse_url ( $source, PHP_URL_PORT ) : '' ),
               'id'     => $m [1],
               'start'  => padEmbedStart ( $url ['query'] ['start'] ?? $url ['query'] ['t'] ?? '' ) ];

    },

    'embed'   => function ( $parts, $opts ) {

      $start = padEmbedStart ( $opts ['at'] ?? '' ) ?: $parts ['start'];

      // The instance is named, not PeerTube alone: it is that site the click loads from.

      return [ 'src'   => "{$parts ['origin']}/videos/embed/{$parts ['id']}?autoplay=1" . ( $start ? "&start={$start}s" : '' ),
               'link'  => "{$parts ['origin']}/w/{$parts ['id']}" . ( $start ? "?start={$start}s" : '' ),
               'name'  => 'PeerTube (' . preg_replace ( '#^https?://#', '', $parts ['origin'] ) . ')',
               'allow' => 'autoplay; fullscreen; picture-in-picture' ];

    },

  ];

?>
