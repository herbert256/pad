<?php

  // The PAD settings: every $pad* setting pad/config/config.php lists, with its default as
  // written there, the comment above it, and the applications whose _config/ change it.
  // The values of passwords and keys are left out.

  $title = 'Settings';

  $settingRows = [];
  $comment     = [];
  $lines       = explode ( "\n", str_replace ( "\r\n", "\n", (string) file_get_contents ( PAD . 'config/config.php' ) ) );
  $count       = count ( $lines );
  $gap         = 0;

  for ( $i = 0; $i < $count; $i++ ) {

    $line = $lines [$i];

    if ( preg_match ( '#^\s*//\s?(.*)$#', $line, $match ) ) {
      if ( $gap > 1 ) $comment = [];
      $comment [] = $match [1];
      $gap = 0;
      continue;
    }

    if ( trim ( $line ) === '' ) {
      $gap++;
      continue;
    }

    if ( ! preg_match ( '/^\s*\$(pad[A-Za-z0-9_]*)\s*=\s*(.*)$/', $line, $match ) ) {
      $comment = [];
      continue;
    }

    // The statement runs on to the line that ends it.

    $value = $match [2];

    while ( ! preg_match ( '/;\s*(\/\/.*)?$/', rtrim ( $value ) ) and $i + 1 < $count )
      $value .= "\n" . $lines [++$i];

    $value = preg_replace ( '/;\s*(\/\/.*)?$/', '', rtrim ( $value ) );

    $settingRows [ $match [1] ] = [ 'name' => $match [1], 'value' => adminRedactValue ( $match [1], $value ),
                                    'about' => $gap <= 1 ? trim ( implode ( ' ', $comment ) ) : '', 'apps' => [] ];

    $comment = [];
    $gap     = 0;

  }

  // Who changes what.

  $overrideRows = [];

  foreach ( adminApps () as $name => $one )
    foreach ( adminConfigOf ( $name ) as $setting ) {

      if ( isset ( $settingRows [ $setting ['name'] ] ) )
        $settingRows [ $setting ['name'] ] ['apps'] [] = [ 'app' => $name ];

      $overrideRows [] = [ 'app' => $name, 'name' => $setting ['name'], 'value' => $setting ['value'],
                           'known' => isset ( $settingRows [ $setting ['name'] ] ) ? 1 : 0 ];

    }

  $settingRows = array_values ( $settingRows );

  foreach ( $settingRows as &$one )
    $one ['appCount'] = count ( $one ['apps'] );

  unset ( $one );

  $settingCount  = count ( $settingRows );
  $overrideCount = count ( $overrideRows );

?>
