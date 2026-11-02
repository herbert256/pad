<?php

  // internetarchive - An item of the Internet Archive - a film, a recording, a book - in the
  // archive's own player: archive.org/details/<identifier>, with a file of the item after
  // it when it has several, and archive.org/embed/<identifier>; named, a bare identifier as
  // well, {embed:internetarchive 'night_of_the_living_dead'}.

  return [

    'name'    => 'the Internet Archive',
    'kind'    => 'video',
    'frame'   => 'https://archive.org',

    'match'   => function ( $source, $named ) {

      $item = '[A-Za-z0-9][A-Za-z0-9._-]{1,99}';

      if ( $named and preg_match ( "#^$item$#D", $source ) )
        return [ 'item' => $source ];

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      if ( $url ['host'] == 'archive.org' and preg_match ( "#^/(?:details|embed)/($item(?:/[^?\#]+)?)/?$#D", $url ['path'], $m ) )
        return [ 'item' => $m [1] ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      $item = implode ( '/', array_map ( 'rawurlencode', array_map ( 'rawurldecode', explode ( '/', $parts ['item'] ) ) ) );

      return [ 'src'   => "https://archive.org/embed/$item",
               'link'  => "https://archive.org/details/$item",
               'allow' => 'autoplay; fullscreen' ];

    },

  ];

?>
