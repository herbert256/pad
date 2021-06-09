<?php

  if (  $pad_seq_random or $pad_tag == 'random' )
    if ( $pad_seq_unique and in_array ($pad_seq_now, $pad_seq_base) )
      return true;

  $pad_seq_base [] = $pad_seq_now;

  if ( ! include 'skip.php' )
    $pad_seq_result [] = $pad_seq_now;

  if ( include 'stop.php')
    return false;

  return true;

?>