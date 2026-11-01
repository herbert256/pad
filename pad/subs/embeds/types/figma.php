<?php

  // figma - A design, a prototype, a FigJam board or a slide deck of Figma in Figma's viewer:
  // figma.com/design/<key>, /proto/<key>, /board/<key>, /slides/<key> and /deck/<key>, the
  // file's name after the key or not, the old /file/<key> as a design, and the same on
  // embed.figma.com. The frame is the address on embed.figma.com with embed-host=pad - the
  // route developers.figma.com/docs/embeds documents - with the node-id of the address, the
  // frame or page it opens on. theme= is the viewer's own - light (Figma's default), dark or
  // system, the visitor's. A file is seen as Figma's sharing settings allow: one that is not
  // open to anyone with the link asks the visitor to log in to Figma, in the frame. A branch
  // of a file is not read - its key is not the file's.

  return [

    'name'    => 'Figma',
    'kind'    => 'document',
    'frame'   => 'https://embed.figma.com',
    'options' => [ 'theme' ],

    'match'   => function ( $source, $named ) {

      if ( ! $url = padEmbedUrl ( $source ) or ! in_array ( $url ['host'], [ 'figma.com', 'embed.figma.com' ], TRUE ) )
        return NULL;

      if ( ! preg_match ( '#^/(file|design|proto|board|slides|deck)/([A-Za-z0-9]{10,64})(?:/(?!branch/)[^/]+)?/?$#D', $url ['path'], $m ) )
        return NULL;

      $node = is_string ( $url ['query'] ['node-id'] ?? NULL ) && preg_match ( '/^(\d{1,12})[-:](\d{1,12})$/D', $url ['query'] ['node-id'], $n )
            ? "{$n [1]}-{$n [2]}" : '';

      return [ 'what' => ( $m [1] == 'file' ) ? 'design' : $m [1], 'key' => $m [2], 'node' => $node ];

    },

    'embed'   => function ( $parts, $opts ) {

      global $padCheckSyntax;

      $theme = strtolower ( trim ( (string) ( is_scalar ( $opts ['theme'] ?? NULL ) ? $opts ['theme'] : '' ) ) );

      if ( ! in_array ( $theme, [ '', 'light', 'dark', 'system' ], TRUE ) ) {
        if ( $padCheckSyntax )
          padError ( "the figma embed has no theme '" . padMakeSafe ( $theme, 20 ) . "' - light, dark or system" );
        $theme = '';
      }

      $path = "/{$parts ['what']}/{$parts ['key']}";
      $node = ( $parts ['node'] !== '' ) ? "node-id={$parts ['node']}" : '';

      return [ 'src'  => "https://embed.figma.com$path?embed-host=pad" . ( $node !== '' ? "&$node" : '' ) . ( $theme !== '' ? "&theme=$theme" : '' ),
               'link' => "https://www.figma.com$path" . ( $node !== '' ? "?$node" : '' ) ];

    },

  ];

?>
