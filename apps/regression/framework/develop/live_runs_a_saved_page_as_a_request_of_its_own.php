<?php

  // Run in the live application saves the template and its PHP as a page of this server and
  // sends the frame there: a request of its own, ?page/<id>, which the page's PHP and the
  // request values reach as they reach any page. The save carries the token of the editor's
  // form, fetched first with its cookie.

  $form   = padCurl ( $padHost . 'live/' );
  $token  = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';
  $jar    = [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ];

  $save   = padCurl ( [
    'url'     => $padHost . 'live/?save',
    'cookies' => $jar,
    'options' => [ 'FOLLOWLOCATION' => FALSE ],
    'post'    => [ 'source'       => "<p>{\$title | upper} for {pad:echo \$who ?? 'nobody'/}</p>",
                   'mode'         => 'php',
                   'php'          => "<?php \$title = 'fruit'; ?>",
                   'padCsrfToken' => $token ]
  ] );

  $page   = (string) ( $save ['headers'] ['Location'] ?? '' );
  $answer = $save ['result'] . ' ' . preg_replace ( '#^.*/live/\?page/[0-9a-f]{20}$#', '?page/<id>', $page ) . ' | '
          . trim ( padCurl ( [ 'url' => "$page&who=Ann", 'cookies' => $jar ] ) ['data'] );

?>
