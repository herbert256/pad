<?php

  $pad_link_app  = $pad_prms_tag ['app']  ?? $app;
  $pad_link_page = $pad_prms_tag ['page'] ?? 'index';
  $pad_link_text = $pad_prms_tag ['text'] ?? $pad_content;

  pad_done (, 'page', TRUE );

  return "<a href=\"$pad_host$pad_script?app=$pad_link_app&page=$pad_link_page\">$pad_link_text</a>";

?>