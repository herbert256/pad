<?php

  $pContent_store_data = $pContent_store [$pName [$p]];

  if ( strpos ( $pContent_store_data, '{@content}' ) !== FALSE ) {
    $pContent_store_data = str_replace('{@content}', $pContent, $pContent_store_data);
    $pContent = '';
  }

  return $pContent_store_data;

?>