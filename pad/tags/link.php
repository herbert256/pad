<?php

  $padLinkApp  = $padPrm [$pad] ['app']  ?? $app;
  $padLinkPage = $padPrm [$pad] ['page'] ?? 'index';
  $padLinkText = $padPrm [$pad] ['text'] ?? $padContent;

  padDone ( 'page', TRUE );

  return "<a href=\"$padHost$padScript?app=$padLinkApp&page=$padLinkPage\">$padLinkText</a>";

?>