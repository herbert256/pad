<?php

  $pLink_app  = $pPrms_tag ['app']  ?? $app;
  $pLink_page = $pPrms_tag ['page'] ?? 'index';
  $pLink_text = $pPrms_tag ['text'] ?? $pContent;

  pDone ( 'page', TRUE );

  return "<a href=\"$pHost$pScript?app=$pLink_app&page=$pLink_page\">$pLink_text</a>";

?>