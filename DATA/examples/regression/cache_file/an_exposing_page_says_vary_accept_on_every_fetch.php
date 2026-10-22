<?php

  // A page that exposes its data answers JSON to an Accept header that asks for it, so every
  // answer of it says Vary: Accept - the second fetch too. The page cache kept it, and a hit,
  // which runs no page PHP, said Vary: Accept-Encoding only: a browser holding the HTML
  // answered a fetch of the page's JSON with it. Apache joins the Vary names with a comma,
  // php -S with a comma and a blank: the blank is taken out before they are compared.

  $exposedUrl = $padHost . 'regression/cache_file/?exposed&padInclude&fresh=' . bin2hex ( random_bytes ( 6 ) );

  function exposedVary ( $url ) {

    foreach ( padCurl ( $url ) ['headers'] ?? [] as $name => $value )
      if ( strtolower ( $name ) == 'vary' )
        return preg_replace ( '/\s*,\s*/', ',', $value );

    return '-';

  }

  $exposedResult = 'first: ' . exposedVary ( $exposedUrl ) . ', second: ' . exposedVary ( $exposedUrl );

?>
