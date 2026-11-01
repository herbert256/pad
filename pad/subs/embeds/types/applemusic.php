<?php

  // applemusic - An album, a playlist, a song, an artist, a music video or a station of Apple
  // Music in Apple's player, which plays previews without an account: music.apple.com/<country>/
  // <what>/<name>/<id> - the name may be left out - a song of an album with ?i=<id> after it,
  // and the old itunes.apple.com form of the same. The player is the address on
  // embed.music.apple.com, framed with the sandbox and the permissions Apple's own embed code
  // gives it (Copy Embed Code on music.apple.com). 4:3, a song 5:2, a music video 16:9,
  // unless ratio= says otherwise.

  return [

    'name'    => 'Apple Music',
    'kind'    => 'audio',
    'frame'   => 'https://embed.music.apple.com',

    'match'   => function ( $source, $named ) {

      if ( ! $url = padEmbedUrl ( $source ) or ! in_array ( $url ['host'], [ 'music.apple.com', 'embed.music.apple.com', 'itunes.apple.com' ], TRUE ) )
        return NULL;

      if ( ! preg_match ( '#^/([a-z]{2})/(album|playlist|song|artist|music-video|station)/(?:([^/]+)/)?(?:id)?(\d{1,15}|[a-z]{2}\.[A-Za-z0-9-]{1,80})/?$#D', $url ['path'], $m ) )
        return NULL;

      $song = is_string ( $url ['query'] ['i'] ?? NULL ) && preg_match ( '/^\d{1,15}$/D', $url ['query'] ['i'] ) ? $url ['query'] ['i'] : '';

      return [ 'country' => $m [1], 'what' => $m [2], 'slug' => $m [3], 'id' => $m [4], 'song' => $song ];

    },

    'embed'   => function ( $parts, $opts ) {

      $slug = ( $parts ['slug'] !== '' ) ? rawurlencode ( rawurldecode ( $parts ['slug'] ) ) . '/' : '';
      $path = "/{$parts ['country']}/{$parts ['what']}/$slug{$parts ['id']}" . ( $parts ['song'] !== '' ? "?i={$parts ['song']}" : '' );

      return [ 'src'     => "https://embed.music.apple.com$path",
               'link'    => "https://music.apple.com$path",
               'ratio'   => match ( TRUE ) { $parts ['what'] == 'music-video' => '16:9',
                                             $parts ['what'] == 'song' or $parts ['song'] !== '' => '5:2',
                                             default => '4:3' },
               'allow'   => 'autoplay *; encrypted-media *; fullscreen *; clipboard-write',
               'sandbox' => 'allow-forms allow-popups allow-same-origin allow-scripts allow-storage-access-by-user-activation allow-top-navigation-by-user-activation' ];

    },

  ];

?>
