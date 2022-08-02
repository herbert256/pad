<?php
     
  $pad_walk = 'next';
  
  include 'walk.php';

  if     ( $pad_tag_result === NULL  ) return  '';
  elseif ( $pad_tag_result === FALSE ) return  $pad_false;
  elseif ( $pad_tag_result === TRUE  ) return  $pad_content;

  if ( array ( $pad_tag_result) )
    return  pad_make_content ( $pad_tag_result );

  return $pad_return;
  
?> 