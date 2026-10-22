<?php

  // A page a shared cache may keep, of an application with one locale in $padLocales: neither
  // Accept-Language nor a padLang cookie can choose another, so the answer to a visitor with
  // that cookie is public and Vary names neither - where Vary said Accept-Language, Cookie
  // and a shared cache kept a copy per Cookie header, and the padLang visitor was answered
  // private. The blank after Vary's commas is taken out, as Apache and php -S join them
  // differently.

  $varyAnswer = padCurl ( [ 'url' => $padHost . 'regression/cache_file/?proxiedone&padInclude&fresh=' . bin2hex ( random_bytes ( 6 ) ),
                            'headers' => [ 'Accept-Language' => 'en', 'Cookie' => 'padLang=en' ] ] );

  $varyControl = '';
  $varyList    = '';

  foreach ( $varyAnswer ['headers'] ?? [] as $varyName => $varyValue )
    if ( strtolower ( $varyName ) == 'cache-control' )
      $varyControl = $varyValue;
    elseif ( strtolower ( $varyName ) == 'vary' )
      $varyList = preg_replace ( '/\s*,\s*/', ',', $varyValue );

  $varyResult = trim ( (string) ( $varyAnswer ['data'] ?? '' ) ) . ' '
              . ( preg_match ( '/^(public|private)\b/', $varyControl, $varyMatch ) ? $varyMatch [1] : "'$varyControl'" )
              . ', Vary: ' . $varyList;

?>
