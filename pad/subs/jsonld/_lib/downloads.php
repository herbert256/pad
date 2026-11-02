<?php
  // Only named public fields of download rows can reach structured metadata.
  function padJsonLdDownloads ( $value, $name, $what, $report ) {
    if ( ! is_array ( $value ) ) { $report ( "The $name of $what must be download rows" ); return NULL; }
    if ( ! array_is_list ( $value ) ) $value = [ $value ];
    if ( count ( $value ) > 100 ) { $report ( 'At most 100 dataset downloads are supported' ); return NULL; }
    $out = [];
    foreach ( $value as $row ) {
      if ( ! is_array ( $row ) ) { $report ( 'A dataset download must be a row' ); continue; }
      $url = $row ['contentUrl'] ?? $row ['url'] ?? NULL;
      if ( ! is_string ( $url ) or ( $url = padJsonLdUrl ( $url ) ) === '' ) { $report ( 'A dataset download requires a safe contentUrl' ); continue; }
      $one = [ '@type' => 'DataDownload', 'contentUrl' => $url ];
      foreach ( [ 'name', 'description', 'encodingFormat', 'contentSize' ] as $field )
        if ( isset ( $row [$field] ) and is_scalar ( $row [$field] ) and ! is_bool ( $row [$field] ) and trim ( (string) $row [$field] ) !== '' ) $one [$field] = trim ( (string) $row [$field] );
      $out [] = $one;
    }
    return $out ?: NULL;
  }
?>
