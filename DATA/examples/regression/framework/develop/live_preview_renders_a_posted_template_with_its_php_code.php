<?php

  // Switched to PHP, Live preview's data pane posts PHP code, as a page's .php, in place of
  // the JSON object: the variables it sets are the template's, and what it echoes stands
  // before the template, as a page's echo does. The post carries the token of the
  // Live's own form, fetched first with its cookie.

  $form  = padCurl ( $padHost . 'live/' );
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';

  $answer = padCurl ( [
    'url'     => $padHost . 'live/?render',
    'cookies' => [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ],
    'post'    => [ 'source'       => '<ul>{array:items}<li>{$name}</li>{/array:items}</ul> {$title | upper}',
                   'mode'         => 'php',
                   'php'          => "<?php\n  echo '<p>' . count ( [ 1, 2 ] ) . '</p>';\n  \$title = 'fruit';\n  \$items = [ [ 'name' => 'apple' ], [ 'name' => 'pear' ] ];\n?>",
                   'data'         => '{ "title": "not the json" }',
                   'padCsrfToken' => $token ]
  ] ) ['data'];

?>
