<?php

  // applepodcasts - A show or an episode of Apple Podcasts in Apple's player:
  // podcasts.apple.com/<country>/podcast/<name>/id<id> - the name and the id before the number
  // may be left out, as the player's own addresses leave them - an episode
  // with ?i=<id> after it, and the old itunes.apple.com form of the same. The player is the
  // address on embed.podcasts.apple.com, framed with the sandbox and the permissions Apple's
  // own embed code gives it (Copy Embed on podcasts.apple.com). A show 4:3, an episode 5:2,
  // unless ratio= says otherwise.

  return [

    'name'    => 'Apple Podcasts',
    'kind'    => 'audio',
    'frame'   => 'https://embed.podcasts.apple.com',

    'match'   => function ( $source, $named ) {

      if ( ! $url = padEmbedUrl ( $source ) or ! in_array ( $url ['host'], [ 'podcasts.apple.com', 'embed.podcasts.apple.com', 'itunes.apple.com' ], TRUE ) )
        return NULL;

      if ( ! preg_match ( '#^/([a-z]{2})/podcast/(?:([^/]+)/)?(?:id)?(\d{1,15})/?$#D', $url ['path'], $m ) )
        return NULL;

      $episode = is_string ( $url ['query'] ['i'] ?? NULL ) && preg_match ( '/^\d{1,15}$/D', $url ['query'] ['i'] ) ? $url ['query'] ['i'] : '';

      return [ 'country' => $m [1], 'slug' => $m [2], 'id' => $m [3], 'episode' => $episode ];

    },

    'embed'   => function ( $parts, $opts ) {

      $slug = ( $parts ['slug'] !== '' ) ? rawurlencode ( rawurldecode ( $parts ['slug'] ) ) . '/' : '';
      $path = "/{$parts ['country']}/podcast/{$slug}id{$parts ['id']}" . ( $parts ['episode'] !== '' ? "?i={$parts ['episode']}" : '' );

      return [ 'src'     => "https://embed.podcasts.apple.com$path",
               'link'    => "https://podcasts.apple.com$path",
               'ratio'   => ( $parts ['episode'] !== '' ) ? '5:2' : '4:3',
               'allow'   => 'autoplay *; encrypted-media *; fullscreen *; clipboard-write',
               'sandbox' => 'allow-forms allow-popups allow-same-origin allow-scripts allow-storage-access-by-user-activation allow-top-navigation-by-user-activation' ];

    },

  ];

?>
