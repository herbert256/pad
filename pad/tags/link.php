<?php

  $padLinkApp  = $padPrmsTag [$pad] ['app']  ?? $app;
  $padLinkPage = $padPrmsTag [$pad] ['page'] ?? 'index';
  $padLinkText = $padPrmsTag [$pad] ['text'] ?? $padContent;

  padDone ( 'page', TRUE );

  return "<a href=\"$padHost$padScript?app=$padLinkApp&page=$padLinkPage\">$padLinkText</a>";

?>