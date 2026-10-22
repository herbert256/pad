<?php

  // A page a shared cache may keep, of an application with several $padLocales, says Vary:
  // Cookie on its public answer as well as Accept-Language: a padLang cookie chooses the
  // locale (lib/locale.php), and the answer to that visitor is private - but a shared cache
  // that kept the guest's English answer, stored under the address and Accept-Language
  // alone, handed it to the next visitor whose padLang=nl cookie chose Dutch, the server
  // never asked (RFC 9111 4.1: only the headers Vary names are matched).

  $varyAnswer = padCurl ( [ 'url' => $padHost . 'regression/cache_file/?proxied&padInclude&fresh=' . bin2hex ( random_bytes ( 6 ) ),
                            'headers' => [ 'Accept-Language' => 'en' ] ] );

  $varyControl = '';
  $varyList    = '';

  foreach ( $varyAnswer ['headers'] ?? [] as $varyName => $varyValue )
    if ( strtolower ( $varyName ) == 'cache-control' )
      $varyControl = $varyValue;
    elseif ( strtolower ( $varyName ) == 'vary' )
      $varyList = $varyValue;

  $varyResult = trim ( (string) ( $varyAnswer ['data'] ?? '' ) ) . ' '
              . ( preg_match ( '/^(public|private)\b/', $varyControl, $varyMatch ) ? $varyMatch [1] : "'$varyControl'" )
              . ', Vary: ' . $varyList;

?>
