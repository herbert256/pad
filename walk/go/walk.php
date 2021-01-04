<?php

  pad_trace ("walk/$pad_walk", "$pad_tag_type/$pad_tag: $pad_content");

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

?>