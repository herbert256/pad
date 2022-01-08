<?php
  
  $pad_return = include PAD . "level/type.php";

  if     ( $pad_return === NULL  ) return  '';
  elseif ( $pad_return === FALSE ) return  $pad_false;
  elseif ( $pad_return === TRUE  ) return  $pad_content;

  if     ( is_object   ( $pad_return ) ) $pad_return = pad_xxx_to_array ( $pad_return );
  elseif ( is_resource ( $pad_return ) ) $pad_return = pad_xxx_to_array ( $pad_return );

  if ( array ( $pad_return ) ) {
    if ( $pad_walk == 'occurence-start' ) {
      $pad_data [$pad_lvl] [$pad_key [$pad_lvl]] = $pad_return;
      return $pad_content;
    } else {
      return  pad_make_content ( $pad_return );
    }
  }

  return $pad_return;

?>