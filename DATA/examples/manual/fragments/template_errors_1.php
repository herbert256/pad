<?php

  // The broken page beside this one, asked the way a tool asks: the error comes back as
  // JSON, and its template part is what this example shows.

  $answer   = padCurl ( [ 'url'     => $padHost . 'manual/?fragments/template_errors_typo&padInclude',
                          'options' => [ 'USERAGENT' => 'curl/8' ] ] );

  $template = json_decode ( $answer ['data'], TRUE ) ['template'] ?? [];

?>
