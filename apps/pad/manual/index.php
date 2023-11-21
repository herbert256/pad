<?php

  $showTitle = TRUE;

  if ( ! isset ( $manual ) ) $manual = '';
  if ( ! isset ( $extra  ) ) $extra = '';
 
  if ( $manual )
    $title = $manual;

  if ( $extra )
    $title .= $extra;

?>