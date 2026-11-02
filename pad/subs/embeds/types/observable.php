<?php
  return [
    'name' => 'Observable', 'kind' => 'code',
    'frame' => [ 'https://observablehq.com', 'https://old.observablehq.com' ],
    'options' => [ 'cell', 'width', 'height' ],
    'match' => function ( $source, $named ) {
      $url = parse_url ( $source );
      if ( ! $url or ( $url ['scheme'] ?? '' ) !== 'https' or ! in_array ( $url ['host'] ?? '', [ 'observablehq.com', 'old.observablehq.com' ], TRUE )
        or isset ( $url ['user'] ) or isset ( $url ['port'] )
        or ! preg_match ( '~^/(?:embed/)?(@[a-z0-9_-]{1,40}/[a-z0-9][a-z0-9-]{0,100}|d/[a-f0-9]{16})(@[1-9][0-9]{0,9})?/?$~D', $url ['path'] ?? '', $m ) ) return NULL;
      $cells = [];
      foreach ( explode ( '&', $url ['query'] ?? '' ) as $part ) {
        [ $key, $value ] = array_pad ( explode ( '=', $part, 2 ), 2, '' );
        if ( urldecode ( $key ) === 'cell' ) $cells [] = urldecode ( $value );
        elseif ( in_array ( urldecode ( $key ), [ 'key', 'api_key', 'apiKey' ], TRUE ) ) return NULL;
      }
      if ( count ( $cells ) > 20 ) return NULL;
      foreach ( $cells as $cell ) if ( ! preg_match ( '/^(?:(?:viewof|mutable) )?[A-Za-z_$][A-Za-z0-9_$]{0,99}$/D', $cell ) ) return NULL;
      return [ 'path' => $m [1] . ( $m [2] ?? '' ), 'cells' => $cells ];
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
      $query = implode ( '&', array_map ( fn ( $cell ) => 'cell=' . rawurlencode ( $cell ), array_unique ( $cells ) ) );
      return [ 'src' => 'https://observablehq.com/embed/' . $parts ['path'] . ( $query === '' ? '' : '?' . $query ),
        'link' => 'https://observablehq.com/' . $parts ['path'], 'ratio' => "$w:$h" ];
    },
  ];
?>
