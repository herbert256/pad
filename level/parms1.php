<?php

  $pad_first   = substr($pad_between, 0, 1);
  $pad_words   = preg_split("/[\s]+/", $pad_between, 2, PREG_SPLIT_NO_EMPTY);
  $pad_tag     = trim($pad_words[0] ?? '');
  $pad_parms   = trim($pad_words[1] ?? '');

  $pad_pair_search = $pad_tag;
  $pad_parms_type  = ( $pad_parms ) ? 'open' : 'none';
  
?>