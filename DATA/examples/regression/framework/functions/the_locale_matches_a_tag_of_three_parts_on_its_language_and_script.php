<?php

  // A language tag of more than two parts - zh-Hant-TW, sr-Latn-RS, de-DE-1996 - is matched
  // on its language and script or region; it matched nothing, not even its language, and the
  // visitor whose browser asked for it got the default locale.

  $padLocales = [ "en", "de", "sr", "zh_Hant" ];
  $chosen     = [];

  foreach ( [ "zh-Hant-TW, en;q=0.5", "sr-Latn-RS, en;q=0.5", "de-DE-1996, en;q=0.5", "x-klingon, de;q=0.5" ] as $header ) {
    $_SERVER ["HTTP_ACCEPT_LANGUAGE"] = $header;
    $chosen [] = padLocaleChoose ();
  }

  $chosen     = implode ( ',', $chosen );
  $padLocales = [];

?>
