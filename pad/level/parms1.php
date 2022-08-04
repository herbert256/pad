<?php

  $pad_first   = substr($pad_between, 0, 1);
  $pad_words   = preg_split("/[\s]+/", $pad_between, 2, PREG_SPLIT_NO_EMPTY);
  $pad_tag     = trim($pad_words[0] ?? '');
  $pad_prms   = trim($pad_words[1] ?? '');

  $pad_pair_search = $pad_tag;
  $pad_prms_type  = ( $pad_prms ) ? 'open' : 'none';
  
?>