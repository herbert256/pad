<?php

  // Safe file uploads: one helper between $_FILES and the disk.
  //
  // padUpload       takes the file of one form field: checks that PHP received it whole and
  //                 as an upload, finds its real type from its content (finfo) - not the
  //                 type the browser claims, nor its name - holds it against the allowed
  //                 types and the size limit, and stores it under a random name in
  //                 DATA/uploads/. Answers the record of the stored file, NULL when the
  //                 field brought no file, FALSE when the file was refused
  // padUploadError  why the field's file was refused - {input type='file'} shows it too
  // padUploadBytes  '2M', '500K', '1.5G' or a number, as bytes
  //
  // Nothing handled $_FILES before: every application checked the claimed type, if
  // anything, and kept the visitor's own file name - which can name a path, collide with
  // another visitor's file, or end in .php.

  function padUpload ( $field, $types = [], $max = '', $dir = 'uploads' ) {

    global $padDirMode, $padFileMode, $padUploadErrors, $padUploadErrorParts;

    unset ( $padUploadErrors [$field], $padUploadErrorParts [$field] );

    // What the call asks for is checked whether a file came or not: a size or a directory
    // it cannot read is the application's mistake, and named on the first request.

    $limit = padUploadBytes ( $max );
    $dir   = trim ( str_replace ( '\\', '/', (string) $dir ), '/' );

    if ( $dir === '' or ! preg_match ( '#^[A-Za-z0-9_-]+(/[A-Za-z0-9_-]+)*$#', $dir ) )
      padError ( "padUpload stores under DATA in a plain directory name - '$dir' is not one" );

    $file = $_FILES [$field] ?? NULL;

    if ( ! is_array ( $file ) or ( $file ['error'] ?? UPLOAD_ERR_NO_FILE ) === UPLOAD_ERR_NO_FILE )
      return NULL;

    // The visitor names the field: one sent as a list - avatar[] where the page takes one
    // avatar - is refused like any file the page cannot take. It was the author's error,
    // and renaming the field turned any upload page into an error report.

    if ( is_array ( $file ['error'] ) )
      return padUploadRefuse ( $field, ':label must be one file', '' );

    if ( in_array ( $file ['error'], [ UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ] ) )
      return padUploadRefuse ( $field, ':label is larger than :n', padUploadSize ( $limit ?: padUploadBytes ( ini_get ( 'upload_max_filesize' ) ) ) );

    if ( $file ['error'] !== UPLOAD_ERR_OK or ! is_uploaded_file ( $file ['tmp_name'] ?? '' ) )
      return padUploadRefuse ( $field, ':label did not arrive whole - please send it again', '' );

    $size = filesize ( $file ['tmp_name'] );

    if ( $limit and $size > $limit )
      return padUploadRefuse ( $field, ':label is larger than :n', padUploadSize ( $limit ) );

    if ( ! class_exists ( 'finfo' ) )
      padError ( 'padUpload needs the fileinfo extension to see what a file really is' );

    $type = ( new finfo ( FILEINFO_MIME_TYPE ) ) -> file ( $file ['tmp_name'] ) ?: 'application/octet-stream';

    if ( $types and ! padUploadTypeAllowed ( $type, (array) $types ) )
      return padUploadRefuse ( $field, ':label must be a file of type :n', implode ( ', ', (array) $types ) );

    if ( ! is_dir ( DATA . $dir ) )
      mkdir ( DATA . $dir, $padDirMode ?? 0755, TRUE );

    $extension = padUploadExtension ( $file ['tmp_name'], $type );
    $stored    = "$dir/" . bin2hex ( random_bytes ( 16 ) ) . ( $extension !== '' ? ".$extension" : '' );

    if ( ! move_uploaded_file ( $file ['tmp_name'], DATA . $stored ) )
      return padUploadRefuse ( $field, ':label could not be stored', '' );

    @chmod ( DATA . $stored, $padFileMode ?? 0644 );

    return [
      'name'      => padUploadName ( $file ['name'] ?? '' ),
      'file'      => $stored,
      'path'      => DATA . $stored,
      'size'      => $size,
      'type'      => $type,
      'extension' => $extension
    ];

  }

  // The refusal is kept like a padValidate message - its text with :label still in it - so
  // the field shows it in the words of its own label.

  function padUploadRefuse ( $field, $text, $arg ) {

    global $padUploadErrors, $padUploadErrorParts;

    $padUploadErrorParts [$field] = [ $text, $arg ];
    $padUploadErrors     [$field] = padValidateText ( [ $text, $arg ], padValidateName ( $field ) );

    return FALSE;

  }

  function padUploadError ( $field ) {

    global $padUploadErrors;

    return $padUploadErrors [$field] ?? '';

  }

  // 'image/png' exactly, or 'image/*' for every image.

  function padUploadTypeAllowed ( $type, $types ) {

    foreach ( $types as $one ) {

      $one = strtolower ( trim ( (string) $one ) );

      if ( $one === strtolower ( $type ) )
        return TRUE;

      if ( str_ends_with ( $one, '/*' ) and str_starts_with ( strtolower ( $type ), substr ( $one, 0, -1 ) ) )
        return TRUE;

    }

    return FALSE;

  }

  // The extension comes from what the file is, never from the name it was sent with.

  function padUploadExtension ( $path, $type ) {

    $known = [ 'image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/webp' => 'webp',
               'image/svg+xml' => 'svg', 'application/pdf' => 'pdf', 'text/plain' => 'txt',
               'text/csv' => 'csv', 'application/zip' => 'zip', 'application/json' => 'json' ];

    if ( isset ( $known [$type] ) )
      return $known [$type];

    $found = ( new finfo ( FILEINFO_EXTENSION ) ) -> file ( $path );
    $found = strtolower ( explode ( '/', (string) $found ) [0] );

    return preg_match ( '/^[a-z0-9]{1,8}$/', $found ) ? $found : 'bin';

  }

  // The name the visitor's file had, for showing: the last part of a path only, without
  // control characters, at most 200 characters. It is never used to store the file.

  function padUploadName ( $name ) {

    $name = basename ( str_replace ( '\\', '/', (string) $name ) );
    $name = preg_replace ( '/[\x00-\x1F\x7F]/', '', $name );

    return mb_substr ( $name, 0, 200 );

  }

  function padUploadBytes ( $size ) {

    $size = trim ( (string) $size );

    if ( $size === '' )
      return 0;

    // A size it cannot read is named; when the error action lets the request go on, the
    // limit is the server's own upload_max_filesize - 0 would mean no limit at all, and the
    // unread size used to go on to an undefined index and a deprecation.

    if ( ! preg_match ( '/^(\d+(?:\.\d+)?)\s*([KMG]?)B?$/i', $size, $match ) ) {
      padError ( "padUpload reads a size like 2M, 500K or 1048576 - not '$size'" );
      return preg_match ( '/^(\d+)\s*([KMG]?)/i', (string) ini_get ( 'upload_max_filesize' ), $server )
           ? (int) $server [1] * ( 1024 ** [ '' => 0, 'K' => 1, 'M' => 2, 'G' => 3 ] [ strtoupper ( $server [2] ) ] )
           : 2097152;
    }

    $power = [ '' => 0, 'K' => 1, 'M' => 2, 'G' => 3 ] [ strtoupper ( $match [2] ) ];

    return (int) round ( (float) $match [1] * ( 1024 ** $power ) );

  }

  function padUploadSize ( $bytes ) {

    foreach ( [ 'GB' => 3, 'MB' => 2, 'KB' => 1 ] as $unit => $power )
      if ( $bytes >= 1024 ** $power )
        return rtrim ( rtrim ( number_format ( $bytes / ( 1024 ** $power ), 1, '.', '' ), '0' ), '.' ) . " $unit";

    return "$bytes bytes";

  }

?>
