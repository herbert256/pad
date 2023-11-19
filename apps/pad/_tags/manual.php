<?php

  $manualLink = $padOpt [$pad] [1];
  $manualText = $padOpt [$pad] [2] ?? $manualLink;

  $manualText = str_replace ( '_', ' ', $manualText);
  $manualText = ucfirst ( $manualText );

  return '<a href="' .  $padGo . 'manual&manual=' . $manualLink . '">' . $manualText . '</a>';

?>