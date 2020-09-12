<?php

  $pad_link_page = $pad_parms_pad ['page'] ?? $pad_parms_seq [0];
  $pad_link_text = $pad_parms_pad ['text'] ?? $pad_link_page;
  $pad_link_app  = $pad_parms_pad ['app']  ?? $app;
  $pad_link_html = "<a href=\"$pad_host$pad_script?app=$pad_link_app&page=$pad_link_page\">$pad_link_text</a>";

  if ( isset ($pad_parms_pad ['page']) ) {
  	   unset ($pad_parms_pad ['page']) ;
  	   unset ($pad_parameters [$pad_lvl] ['parms_pad'] ['page']) ;
  }

  return $pad_link_html;

?>