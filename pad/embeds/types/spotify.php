<?php

  // spotify - A track, an album, a playlist, an artist, an episode or a show of Spotify in
  // its player, which plays a preview without an account: open.spotify.com/<what>/<id>, an
  // intl-xx/ before it too, and the spotify:<what>:<id> form of the app. 2:1 unless ratio=
  // says otherwise.

  return [

    'name'    => 'Spotify',
    'kind'    => 'audio',
    'frame'   => 'https://open.spotify.com',

    'match'   => function ( $source, $named ) {

      $what = '(track|album|playlist|artist|episode|show)';

      if ( preg_match ( "/^spotify:$what:([A-Za-z0-9]{22})$/D", $source, $m ) )
        return [ 'what' => $m [1], 'id' => $m [2] ];

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      if ( $url ['host'] == 'open.spotify.com' and preg_match ( "#^/(?:intl-[a-z]{2}(?:-[a-z]{2})?/)?(?:embed/)?$what/([A-Za-z0-9]{22})/?$#Di", $url ['path'], $m ) )
        return [ 'what' => strtolower ( $m [1] ), 'id' => $m [2] ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      return [ 'src'   => "https://open.spotify.com/embed/{$parts ['what']}/{$parts ['id']}",
               'link'  => "https://open.spotify.com/{$parts ['what']}/{$parts ['id']}",
               'ratio' => '2:1',
               'allow' => 'autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture' ];

    },

  ];

?>
