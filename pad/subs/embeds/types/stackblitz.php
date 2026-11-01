<?php

  // stackblitz - A project of StackBlitz, its code and what it makes running in StackBlitz's
  // editor: stackblitz.com/edit/<project> and a repository of GitHub opened there,
  // stackblitz.com/github/<owner>/<repository>, a /tree/<branch>/<folder> after it or not.
  // The frame is the address with embed=1, as developer.stackblitz.com's embedding guide
  // gives it. file= is the file open in the editor - a ?file= in the address says it too -
  // and view= what is shown: editor, preview or both (the default, side by side where there
  // is room).

  return [

    'name'    => 'StackBlitz',
    'kind'    => 'code',
    'frame'   => 'https://stackblitz.com',
    'options' => [ 'file', 'view' ],

    'match'   => function ( $source, $named ) {

      if ( ! $url = padEmbedUrl ( $source ) or $url ['host'] != 'stackblitz.com' )
        return NULL;

      $file = is_string ( $url ['query'] ['file'] ?? NULL ) ? $url ['query'] ['file'] : '';

      if ( preg_match ( '#^/edit/([A-Za-z0-9_-]{1,100})/?$#D', $url ['path'], $m ) )
        return [ 'route' => "edit/{$m [1]}", 'file' => $file ];

      if ( preg_match ( '#^/github/([A-Za-z0-9_.-]{1,100}/[A-Za-z0-9_.-]{1,100}(?:/tree/[A-Za-z0-9_./-]{1,200})?)/?$#D', $url ['path'], $m )
           and ! str_contains ( $m [1], '..' ) )
        return [ 'route' => "github/{$m [1]}", 'file' => $file ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      global $padCheckSyntax;

      $view = strtolower ( trim ( (string) ( is_scalar ( $opts ['view'] ?? NULL ) ? $opts ['view'] : '' ) ) );
      $file = trim ( (string) ( is_scalar ( $opts ['file'] ?? NULL ) ? $opts ['file'] : $parts ['file'] ) );

      if ( ! in_array ( $view, [ '', 'both', 'editor', 'preview' ], TRUE ) ) {
        if ( $padCheckSyntax )
          padError ( "the stackblitz embed has no view '" . padMakeSafe ( $view, 20 ) . "' - editor, preview or both" );
        $view = '';
      }

      $open = ( $file !== '' ) ? 'file=' . rawurlencode ( $file ) : '';

      return [ 'src'   => "https://stackblitz.com/{$parts ['route']}?embed=1" . ( $open !== '' ? "&$open" : '' )
                          . ( in_array ( $view, [ 'editor', 'preview' ], TRUE ) ? "&view=$view" : '' ),
               'link'  => "https://stackblitz.com/{$parts ['route']}" . ( $open !== '' ? "?$open" : '' ),
               'allow' => 'clipboard-write' ];

    },

  ];

?>
