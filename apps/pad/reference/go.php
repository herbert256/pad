<?php

  if ( ! isset ( $go ) )
    return;

  if ( ! isset ( $item ) ) $item = '';
  if ( ! isset ( $next ) ) $next = '';

  $title = $for;

  if ( $item ) $title .= " - $item";
  if ( $next ) $title .= " - $next";


?>