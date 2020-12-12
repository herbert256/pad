<?php

  if ( $pad_walk == 'start' ) {

    $pad_walk = 'end';

    return TRUE;

  } else {

    $pad_html[$pad_lvl-1] = str_replace('###%%%close_parms%%%###', $pad_content, $pad_html[$pad_lvl-1]);

    $pad_content = '';

    return;

  }
  
?>