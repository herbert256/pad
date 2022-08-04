<?php

  if ( $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pad_html[$pad-1] = str_replace('###%%%close_parms%%%###', $pad_content, $pad_html[$pad-1]);

  $pad_content = '';
  
?>