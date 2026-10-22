<?php

  // A locale of three parts - zh_Hant_TW, sr-Latn-RS - and one written with the charset or
  // modifier setlocale takes - nl_NL.UTF-8, de_DE@euro - is looked for as its whole tag, its
  // language with the script, with the region, and its language alone, and a listed one is
  // chosen for the tag that names it. Each was no locale at all: no catalog was read, not
  // even zh.json or nl.json, and ?lang=zh-Hant-TW served the default language.

  $r = json_encode ( [ padLocaleCandidates ( 'zh_Hant_TW' ), padLocaleCandidates ( 'sr-latn-rs' ),
                       padLocaleCandidates ( 'nl_NL.UTF-8' ), padLocaleCandidates ( 'de_DE@euro' ),
                       padLocaleCandidates ( 'nl_NLD_X' ),
                       padLocaleMatch ( 'zh-Hant-TW', [ 'en', 'zh_Hant_TW' ] ),
                       padLocaleMatch ( 'sr-Latn-RS', [ 'en', 'sr', 'sr_Latn_RS' ] ) ] );

?>
