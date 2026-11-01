<?php

  // The pages the editor saves: DATA/live/<id>.pad - the template - with <id>.php, the PHP
  // code, or <id>.data, the data text. The id is made of what they hold, so the same page
  // saved twice is one page, and two windows never write over each other's. A page is
  // kept a day after it was last saved.

  function liveDir () {

    return DATA . 'live/';

  }

  function liveId ( $source, $mode, $body ) {

    return substr ( hash ( 'sha256', "$mode\0$source\0$body" ), 0, 20 );

  }

  function liveValidId ( $id ) {

    return is_string ( $id ) and preg_match ( '/^[0-9a-f]{20}$/D', $id ) and is_file ( liveDir () . "$id.pad" );

  }

  function liveSweep () {

    foreach ( glob ( liveDir () . '*' ) ?: [] as $file )
      if ( is_file ( $file ) and filemtime ( $file ) < time () - 86400 )
        @unlink ( $file );

  }

  // The data text read as the playground reads it, as padData reads a text: its indent and a
  // byte order mark taken off, its type sniffed (padContentType) and the reader of that type
  // run in a function of its own. A record's keys are the variables; a list, which has no
  // names of its own - CSV, a JSON or YAML array - is the variable rows.

  function liveData ( $text ) {

    $read = static function ( $data ) {
      $name = 'rows';
      $type = padContentType ( $data );
      $read = include PAD . "data/$type.php";
      return is_array ( $read ) ? $read : [];
    };

    $text = padDataText ( (string) $text );
    $data = ( $text === '' ) ? [] : $read ( $text );

    if ( $data and ! array_filter ( array_keys ( $data ), 'is_string' ) )
      $data = [ 'rows' => $data ];

    return $data;

  }

?>
