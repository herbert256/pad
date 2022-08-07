<?php

  $pContentStore_data = $pContentStore [$pName [$p]];

  if ( strpos ( $pContentStore_data, '{@content}' ) !== FALSE ) {
    $pContentStore_data = str_replace('{@content}', $pContent, $pContentStore_data);
    $pContent = '';
  }

  return $pContentStore_data;

?>