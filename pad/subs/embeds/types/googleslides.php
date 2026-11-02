<?php

  // googleslides - Published Google Slides presentations loaded only after the visitor chooses to view them.

  return [
    'name' => 'Google Slides', 'kind' => 'document', 'frame' => 'https://docs.google.com',
    'match' => function ( $source, $named ) {
      $url = parse_url ( $source );
      if ( ! $url or ( $url ['scheme'] ?? '' ) !== 'https' or ( $url ['host'] ?? '' ) !== 'docs.google.com'
        or isset ( $url ['user'] ) or isset ( $url ['port'] ) or ! preg_match ( '~^/presentation/d/e/(2PACX-[A-Za-z0-9_-]{15,200})/(?:pub|embed)/?$~D', $url ['path'] ?? '', $m ) ) return NULL;
      return [ 'id' => $m [1] ];
    },
    'embed' => fn ( $parts, $opts ) => [ 'src' => 'https://docs.google.com/presentation/d/e/' . $parts ['id'] . '/embed?start=false&loop=false&delayms=3000',
      'link' => 'https://docs.google.com/presentation/d/e/' . $parts ['id'] . '/pub', 'allow' => 'fullscreen' ],
  ];
?>
