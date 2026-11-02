<?php

  // The pieces of the embed subsystem needed where no embed is written: the providers - the
  // files of subs/embeds/types/ (padKinds, lib/subsystem.php) - a provider's file, and the
  // origins a Content Security Policy has to allow for their frames, which an application's
  // _config/config.php puts in its $padCsp:
  //
  //   $padCsp = "default-src 'self'; script-src 'self' 'nonce'; style-src 'self' 'nonce'; "
  //           . 'frame-src ' . implode ( ' ', padEmbedSources () );
  //
  // The rest of the embeds is the subsystem in subs/embeds/, its library subs/embeds/_lib/ loaded when
  // a page writes one.

  function padEmbedProviders () {

    // auto selects a provider; it is not itself a remote provider.
    return array_values ( array_diff ( padKinds ( 'embeds' ), [ 'auto' ] ) );

  }

  // A provider's file, read once per request, with what it left out filled in: a video,
  // its frame of no origin of its own, known by its host, no options beside the common ones.

  function padEmbedProvider ( $name ) {

    static $providers = [];

    if ( ! padKind ( 'embeds', $name ) )
      return NULL;

    return $providers [$name] ??= ( include PAD . "subs/embeds/types/$name.php" ) + [ 'kind' => 'video', 'frame' => '', 'generic' => FALSE, 'options' => [] ];

  }

  // The origins the files of subs/embeds/types/ name for their frames, for frame-src. A PeerTube
  // instance is one's own choosing and is added by hand, and so is https://i.ytimg.com in
  // img-src for a page that shows a YouTube thumbnail (thumbnail); a file of the web - a
  // sound, a video, a PDF - needs its origin in media-src or frame-src.

  function padEmbedSources () {

    $frames = [];

    foreach ( padEmbedProviders () as $name )
      foreach ( (array) padEmbedProvider ( $name ) ['frame'] as $origin )
        if ( $origin !== '' )
          $frames [$origin] = TRUE;

    return array_keys ( $frames );

  }

?>
