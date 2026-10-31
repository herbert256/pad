<?php

  // codepen - A pen of CodePen, its result running in CodePen's frame:
  // codepen.io/<user>/pen/<id>, a team's codepen.io/team/<team>/pen/<id>, and the /full/,
  // /details/ and /embed/ forms of them. tab=
  // names the panes shown - result (the default), html, css, js, or two of them, 'html,
  // result'.

  return [

    'name'    => 'CodePen',
    'kind'    => 'code',
    'frame'   => 'https://codepen.io',
    'options' => [ 'tab' ],

    'match'   => function ( $source, $named ) {

      if ( ! $url = padEmbedUrl ( $source ) )
        return NULL;

      if ( $url ['host'] == 'codepen.io' and preg_match ( '#^/((?:team/)?[\w-]+)/(?:pen|full|details|embed)/([A-Za-z0-9]{3,12})/?$#D', $url ['path'], $m ) )
        return [ 'user' => $m [1], 'id' => $m [2] ];

      return NULL;

    },

    'embed'   => function ( $parts, $opts ) {

      global $padCheckSyntax;

      $tab = strtolower ( preg_replace ( '/\s+/', '', (string) ( $opts ['tab'] ?? '' ) ) ) ?: 'result';

      if ( ! preg_match ( '/^(html|css|js|result)(,(html|css|js|result))?$/D', $tab ) ) {
        if ( $padCheckSyntax )
          padError ( "the codepen embed has no tab '" . padMakeSafe ( $tab, 20 ) . "' - result, html, css or js, or two of them" );
        $tab = 'result';
      }

      return [ 'src'   => "https://codepen.io/{$parts ['user']}/embed/{$parts ['id']}?default-tab=" . rawurlencode ( $tab ),
               'link'  => "https://codepen.io/{$parts ['user']}/pen/{$parts ['id']}",
               'allow' => 'fullscreen' ];

    },

  ];

?>
