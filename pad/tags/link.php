<?php

  $pad_link_app  = $pad_parms_tag ['app']  ?? $app;
  $pad_link_page = $pad_parms_tag ['page'] ?? 'index';
  $pad_link_text = $pad_parms_tag ['text'] ?? $pad_content;

  pad_set_arr_var ( 'options_done', 'page', TRUE );

  return "<a href=\"$pad_host$pad_script?app=$pad_link_app&page=$pad_link_page\">$pad_link_text</a>";

?>