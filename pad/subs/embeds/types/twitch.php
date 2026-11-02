<?php

  // twitch - A live channel, a video, a collection or a clip of Twitch in Twitch's player:
  // twitch.tv/<channel>, twitch.tv/videos/<id>, twitch.tv/collections/<id>,
  // twitch.tv/<channel>/clip/<slug>, clips.twitch.tv/<slug> and the player's own addresses,
  // player.twitch.tv/?channel=, ?video= and ?collection=, clips.twitch.tv/embed?clip=; named,
  // a channel's name as well, {embed:twitch 'twitchgaming'}. at= - or a ?t= in the address - is the
  // moment a video starts at. Twitch plays only on the host names it is told (parent=, as
  // dev.twitch.tv/docs/embed/video-and-clips says): those of $padHosts, else the page's own
  // (padEmbedHosts), and domains= adds names - a page shown in a frame of another site.
  // Where no name is known - offline, or a static copy without $padHosts - the poster links
  // to Twitch instead of holding a player that would refuse to play.

  return [

    'name'    => 'Twitch',
    'kind'    => 'video',
    'frame'   => [ 'https://player.twitch.tv', 'https://clips.twitch.tv' ],
    'options' => [ 'at', 'domains' ],

    'match'   => function ( $source, $named ) {

      $channel = '[A-Za-z0-9_]{3,25}';
      $clip    = '[A-Za-z0-9_-]{3,100}';

      if ( $named and preg_match ( "/^$channel$/D", $source ) )
        return [ 'what' => 'channel', 'id' => strtolower ( $source ), 'start' => 0 ];

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      $start = padEmbedStart ( $url ['query'] ['t'] ?? $url ['query'] ['time'] ?? '' );
      $query = fn ( $key, $shape ) => ( is_string ( $url ['query'] [$key] ?? NULL ) and preg_match ( "/^$shape$/D", $url ['query'] [$key] ) ) ? $url ['query'] [$key] : NULL;

      if ( $url ['host'] == 'player.twitch.tv' and in_array ( $url ['path'], [ '', '/' ], TRUE ) ) {
        if ( $id = $query ( 'channel', $channel ) )
          return [ 'what' => 'channel', 'id' => strtolower ( $id ), 'start' => 0 ];
        if ( $id = $query ( 'video', 'v?\d{1,12}' ) )
          return [ 'what' => 'video', 'id' => ltrim ( $id, 'v' ), 'start' => $start ];
        if ( $id = $query ( 'collection', '[A-Za-z0-9_-]{4,40}' ) )
          return [ 'what' => 'collection', 'id' => $id, 'start' => 0 ];
        return NULL;
      }

      if ( $url ['host'] == 'clips.twitch.tv' ) {
        if ( $url ['path'] == '/embed' )
          return ( $id = $query ( 'clip', $clip ) ) ? [ 'what' => 'clip', 'id' => $id, 'start' => 0 ] : NULL;
        return preg_match ( "#^/($clip)/?$#D", $url ['path'], $m ) ? [ 'what' => 'clip', 'id' => $m [1], 'start' => 0 ] : NULL;
      }

      if ( $url ['host'] != 'twitch.tv' )
        return NULL;

      if ( preg_match ( '#^/videos/(\d{1,12})/?$#D', $url ['path'], $m ) )
        return [ 'what' => 'video', 'id' => $m [1], 'start' => $start ];

      if ( preg_match ( '#^/collections/([A-Za-z0-9_-]{4,40})/?$#D', $url ['path'], $m ) )
        return [ 'what' => 'collection', 'id' => $m [1], 'start' => 0 ];

      if ( preg_match ( "#^/$channel/clip/($clip)/?$#D", $url ['path'], $m ) )
        return [ 'what' => 'clip', 'id' => $m [1], 'start' => 0 ];

      // A channel is the first part of the path alone - but for the pages of Twitch's own
      // that stand there.

      if ( preg_match ( "#^/($channel)/?$#D", $url ['path'], $m )
           and ! in_array ( strtolower ( $m [1] ), [ 'directory', 'downloads', 'drops', 'friends', 'inventory', 'jobs', 'login', 'messages', 'moderator',
                                                     'p', 'popout', 'prime', 'search', 'settings', 'signup', 'store', 'subscriptions', 'turbo',
                                                     'videos', 'wallet', 'collections', 'embed', 'broadcast', 'following' ], TRUE ) )
        return [ 'what' => 'channel', 'id' => strtolower ( $m [1] ), 'start' => 0 ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      $start = padEmbedStart ( $opts ['at'] ?? '' ) ?: $parts ['start'];
      $time  = ( $start and $parts ['what'] == 'video' ) ? sprintf ( '%dh%dm%ds', intdiv ( $start, 3600 ), intdiv ( $start % 3600, 60 ), $start % 60 ) : '';
      $id    = rawurlencode ( $parts ['id'] );

      $link = match ( $parts ['what'] ) {
        'video'      => "https://www.twitch.tv/videos/$id" . ( $time !== '' ? "?t=$time" : '' ),
        'collection' => "https://www.twitch.tv/collections/$id",
        'clip'       => "https://clips.twitch.tv/$id",
        default      => "https://www.twitch.tv/$id" };

      $hosts = padEmbedHosts ( $opts ['domains'] ?? '', 'twitch' );

      if ( ! $hosts )
        return [ 'mode' => 'link', 'src' => '', 'link' => $link ];

      $parent = implode ( '', array_map ( fn ( $host ) => '&parent=' . rawurlencode ( $host ), $hosts ) );

      $src = match ( $parts ['what'] ) {
        'video'      => "https://player.twitch.tv/?video=v$id$parent&autoplay=true" . ( $time !== '' ? "&time=$time" : '' ),
        'collection' => "https://player.twitch.tv/?collection=$id$parent&autoplay=true",
        'clip'       => "https://clips.twitch.tv/embed?clip=$id$parent&autoplay=true",
        default      => "https://player.twitch.tv/?channel=$id$parent&autoplay=true" };

      return [ 'src' => $src, 'link' => $link, 'allow' => 'autoplay; fullscreen' ];

    },

  ];

?>
