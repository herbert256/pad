<?php

  // observable - Classic Observable notebooks or selected cells loaded only after visitor consent.

  return [
    'name' => 'Observable', 'kind' => 'code',
    'frame' => [ 'https://observablehq.com', 'https://old.observablehq.com' ],
    'options' => [ 'cell', 'width', 'height' ],
    'match' => function ( $source, $named ) {
      $url = parse_url ( $source );
      if ( ! $url or ( $url ['scheme'] ?? '' ) !== 'https' or ! in_array ( $url ['host'] ?? '', [ 'observablehq.com', 'old.observablehq.com' ], TRUE )
        or isset ( $url ['user'] ) or isset ( $url ['port'] ) ) return NULL;
      $path = $url ['path'] ?? '';
      $embedded = str_starts_with ( $path, '/embed/' );
      if ( $embedded ) $path = substr ( $path, 6 );
      if ( ! preg_match ( '~^/(@[a-z0-9_-]{1,40}/[a-z0-9][a-z0-9-]{0,100}|(?:d/)?[a-f0-9]{16})(@[1-9][0-9]{0,9})?/?$~D', $path, $m ) ) return NULL;
      if ( ! $embedded and ! str_starts_with ( $m [1], '@' ) and ! str_starts_with ( $m [1], 'd/' ) ) return NULL;
      // Notebook IDs have d/ in public links, but no d/ in iframe URLs.
      $path = ( str_starts_with ( $m [1], 'd/' ) ? substr ( $m [1], 2 ) : $m [1] ) . ( $m [2] ?? '' );
      $cells = [];
      foreach ( explode ( '&', $url ['query'] ?? '' ) as $part ) {
        [ $key, $value ] = array_pad ( explode ( '=', $part, 2 ), 2, '' );
        $key = urldecode ( $key );
        if ( $key === 'cell' ) $cells [] = urldecode ( $value );
        elseif ( $key === 'cells' ) $cells = array_merge ( $cells, array_map ( 'trim', explode ( ',', urldecode ( $value ) ) ) );
        elseif ( in_array ( $key, [ 'key', 'api_key', 'apiKey' ], TRUE ) ) return NULL;
        if ( count ( $cells ) > 20 ) return NULL;
      }
      foreach ( $cells as $cell ) if ( ! preg_match ( '/^(?:(?:viewof|mutable) )?[A-Za-z_$][A-Za-z0-9_$]{0,99}$/D', $cell ) ) return NULL;
      return [ 'path' => $path, 'cells' => $cells ];
    },
    'embed' => function ( $parts, $opts ) {
      $cells = $parts ['cells'];
      if ( isset ( $opts ['cell'] ) ) {
        if ( ! is_string ( $opts ['cell'] ) ) return [ 'src' => '', 'link' => '' ];
        $cells = array_map ( 'trim', explode ( ',', $opts ['cell'] ) );
      }
      if ( count ( $cells ) > 20 ) return [ 'src' => '', 'link' => '' ];
      foreach ( $cells as $cell ) if ( ! preg_match ( '/^(?:(?:viewof|mutable) )?[A-Za-z_$][A-Za-z0-9_$]{0,99}$/D', $cell ) ) return [ 'src' => '', 'link' => '' ];
      $w = $opts ['width'] ?? 640; $h = $opts ['height'] ?? 400;
      foreach ( [ $w, $h ] as $n ) if ( ! is_scalar ( $n ) or ! preg_match ( '/^\d{2,4}$/D', (string) $n ) or $n < 100 or $n > 2000 ) return [ 'src' => '', 'link' => '' ];
      $cells = array_values ( array_unique ( $cells ) );
      $query = $cells ? ( count ( $cells ) === 1 ? 'cell=' : 'cells=' ) . rawurlencode ( implode ( ',', $cells ) ) : '';
      return [ 'src' => 'https://observablehq.com/embed/' . $parts ['path'] . ( $query === '' ? '' : '?' . $query ),
        'link' => 'https://observablehq.com/' . ( str_starts_with ( $parts ['path'], '@' ) ? '' : 'd/' ) . $parts ['path'], 'ratio' => "$w:$h" ];
    },
  ];
?>
