<?php

  // The catalogs of a locale with a script are _lang/zh_Hant.json and sr_Latn.json, a script
  // written with one capital as a region is written in capitals. The script was upper-cased
  // too, and zh_HANT.json is a file a file system that tells cases apart does not have.

  $r = json_encode ( [ padLocaleCandidates ( 'zh-Hant' ), padLocaleCandidates ( 'sr_LATN' ), padLocaleCandidates ( 'nl-be' ), padLocaleCandidates ( 'es-419' ) ] );

?>
