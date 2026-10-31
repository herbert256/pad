<?php

  // soundcloud - A track or a set of SoundCloud in its visual player:
  // soundcloud.com/<artist>/<track> and soundcloud.com/<artist>/sets/<set>. 2:1 unless
  // ratio= says otherwise.

  return [

    'name'    => 'SoundCloud',
    'kind'    => 'audio',
    'frame'   => 'https://w.soundcloud.com',

    'match'   => function ( $source, $named ) {

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      if ( $url ['host'] == 'soundcloud.com' and preg_match ( '#^/([\w-]+)/((?:sets/)?[\w-]+)/?$#D', $url ['path'], $m )
           and ! in_array ( $m [1], [ 'discover', 'search', 'you', 'pages', 'settings', 'upload' ], TRUE ) )
        return [ 'path' => "{$m [1]}/{$m [2]}" ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      $track = "https://soundcloud.com/{$parts ['path']}";

      return [ 'src'   => 'https://w.soundcloud.com/player/?url=' . rawurlencode ( $track ) . '&auto_play=true&visual=true',
               'link'  => $track,
               'ratio' => '2:1',
               'allow' => 'autoplay' ];

    },

  ];

?>
