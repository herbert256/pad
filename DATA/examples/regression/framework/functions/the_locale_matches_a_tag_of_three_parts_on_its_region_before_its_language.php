<?php

  // A tag of three parts is matched on its language and script, then on its language and
  // region, then on its language alone: zh-Hans-CN with zh_TW and zh_CN on offer is zh_CN.
  // The region was dropped with the parts after the script, and the first zh_ won.

  $padLocales = [ "en", "zh_TW", "zh_CN", "sr_Latn", "sr_RS" ];
  $chosen     = [];

  foreach ( [ "zh-Hans-CN", "zh-Hant-TW", "sr-Latn-RS", "sr-Cyrl-RS", "zh-Hans-SG" ] as $header ) {
    $_SERVER ["HTTP_ACCEPT_LANGUAGE"] = $header;
    $chosen [] = padLocaleChoose ();
  }

  $chosen     = implode ( ',', $chosen );
  $padLocales = [];

?>
