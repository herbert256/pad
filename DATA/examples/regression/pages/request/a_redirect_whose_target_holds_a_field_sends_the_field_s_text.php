<?php

  // A {redirect} whose target holds a field sends the text the field holds, and the {?t}
  // query fragment as the url's own name=value: the target went into the Location header as
  // the tag's parameter holds it - the field escaped for a page, , = | @ ' " \ as their
  // stand-ins - so q=O&#039;Neil cut q at the &, n=a,b arrived as a private-use character
  // and {?t} sent one parameter named t\u{E03D}Tom+%26+Jerry with no value.

  $rfHop  = fn ( $form ) => padCurl ( [ 'url' => $padGoExt . "request/hopfield&form=$form&padInclude", 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  // As JSON, so a stand-in shows as the \u escape it is - the page's own write-out would
  // turn it into its character.

  $rfCut  = fn ( $hop ) => $hop ['result'] . ' ' . json_encode ( substr ( $hop ['headers'] ['Location'] ?? '', strlen ( $padHost . $padApp . '/' ) ), JSON_UNESCAPED_SLASHES );

  $rfText = $rfCut ( $rfHop ( 'text' ) );
  $rfUrl  = $rfCut ( $rfHop ( 'url' ) );

?>
