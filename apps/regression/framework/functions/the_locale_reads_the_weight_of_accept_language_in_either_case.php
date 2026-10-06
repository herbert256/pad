<?php

  // The weight of a language is q in any case - a parameter name is case-insensitive in
  // HTTP - so Q=0.1 is a low weight; read only in lower case, it stood at 1 and won.

  $padLocales = [ "en", "nl", "de" ];

  $_SERVER ["HTTP_ACCEPT_LANGUAGE"] = "nl;Q=0.1, en;Q=0.5, de;q=0.2";
  $upper = padLocaleChoose ();

  $_SERVER ["HTTP_ACCEPT_LANGUAGE"] = "de;Q=0, nl;q=0.3";
  $zero = padLocaleChoose ();

  $padLocales = [];

?>
