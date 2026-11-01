<?php

  // tiktok - A video or a photo post of TikTok in TikTok's embed player, upright (9:16) and
  // kept to the width of a phone: tiktok.com/@<user>/video/<id>, /@<user>/photo/<id>, the
  // player's own /player/v1/<id> and the older /embed/v2/<id>; named, a bare id as well,
  // {tiktok '6718335390845095173'} - the post's number, nothing else. The player is the one
  // developers.tiktok.com/doc/embed-player documents, with autoplay=1 - it is the click that
  // plays - and rel=0, the author's own videos at the end rather than recommendations. A
  // short vm.tiktok.com link is no address of a post: it has to be followed to find one.

  return [

    'name'    => 'TikTok',
    'kind'    => 'video',
    'frame'   => 'https://www.tiktok.com',

    'match'   => function ( $source, $named ) {

      if ( $named and preg_match ( '/^\d{15,21}$/D', $source ) )
        return [ 'id' => $source, 'user' => '', 'what' => 'video' ];

      if ( ! $url = padEmbedUrl ( $source ) or $url ['host'] != 'tiktok.com' )
        return NULL;

      if ( preg_match ( '#^/@([A-Za-z0-9_.]{1,24})/(video|photo)/(\d{15,21})/?$#D', $url ['path'], $m ) )
        return [ 'id' => $m [3], 'user' => $m [1], 'what' => $m [2] ];

      if ( preg_match ( '#^/(?:player/v1|embed/v2|embed)/(\d{15,21})/?$#D', $url ['path'], $m ) )
        return [ 'id' => $m [1], 'user' => '', 'what' => 'video' ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      $player = "https://www.tiktok.com/player/v1/{$parts ['id']}";

      return [ 'src'   => "$player?autoplay=1&rel=0",
               'link'  => ( $parts ['user'] !== '' ) ? "https://www.tiktok.com/@{$parts ['user']}/{$parts ['what']}/{$parts ['id']}" : $player,
               'ratio' => '9:16',
               'allow' => 'autoplay; fullscreen' ];

    },

  ];

?>
