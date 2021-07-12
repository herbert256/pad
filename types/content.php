<?php

  $pad_content_store_data = $pad_content_store [$pad_name];

  if ( strpos ( $pad_content_store_data, '{@content}' ) !== FALSE ) {
    $pad_content_store_data = str_replace('{@content}', $pad_content, $pad_content_store_data);
    $pad_content = '';
  }

  return $pad_content_store_data;

?>