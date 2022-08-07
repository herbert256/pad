<?php

  $pLink_app  = $pPrmsTag[$p] ['app']  ?? $app;
  $pLink_page = $pPrmsTag[$p] ['page'] ?? 'index';
  $pLink_text = $pPrmsTag[$p] ['text'] ?? $pContent;

  pDone ( 'page', TRUE );

  return "<a href=\"$pHost$pScript?app=$pLink_app&page=$pLink_page\">$pLink_text</a>";

?>