<?php

  $padLink_app  = $padPrmsTag [$pad] ['app']  ?? $app;
  $padLink_page = $padPrmsTag [$pad] ['page'] ?? 'index';
  $padLink_text = $padPrmsTag [$pad] ['text'] ?? $padContent;

  pDone ( 'page', TRUE );

  return "<a href=\"$padHost$padScript?app=$padLink_app&page=$padLink_page\">$padLink_text</a>";

?>