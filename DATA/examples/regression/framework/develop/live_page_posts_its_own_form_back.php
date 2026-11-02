<?php

  // A page saved in the live application is a page like any other: a {pad:form} in it posts back
  // to it, with the page's own CSRF token, and the posted value is there on the way back. Its
  // data is YAML, read as PAD reads data: the key title is the page's variable.

  $form  = padCurl ( $padHost . 'live/' );
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';
  $jar   = [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ];

  $first = padCurl ( [
    'url'     => $padHost . 'live/?save',
    'cookies' => $jar,
    'post'    => [ 'source'       => "<p>{\$title}: {pad:echo \$word ?? 'nothing yet'/}</p>{pad:form 'hi'}{pad:input 'word'/}{/pad:form}",
                   'mode'         => 'data',
                   'data'         => "title: yaml\nkind: data",
                   'padCsrfToken' => $token ]
  ] );

  $page  = $first ['info'] ['url'] ?? '';
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $first ['data'], $m ) ? $m [1] : '';
  $back  = padCurl ( [ 'url' => $page, 'cookies' => $jar,
                       'post' => [ 'padCsrfToken' => $token, 'padForm' => 'hi', 'word' => 'hello' ] ] );

  $p      = fn ( $html ) => preg_match ( '#<p>.*?</p>#', $html, $m ) ? $m [0] : 'no page';
  $answer = $p ( $first ['data'] ) . ' | ' . $p ( $back ['data'] );

?>
