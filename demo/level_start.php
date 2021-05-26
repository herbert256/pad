<?php

  if ( ! pad_local() ) 
    return;

  $pad_demo_source [$pad_lvl] = "{" . $pad_between;

  if ( $pad_pair )
    $pad_demo_source [$pad_lvl] .= '}' . $pad_content. '{/' . $pad_between2; 
  else
    $pad_demo_source [$pad_lvl] .= '/';

  $pad_demo_source [$pad_lvl] .= '}';

?>