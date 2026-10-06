<?php

  // Files dropped on the tree, into one directory of the application. A file that exists
  // there is not overwritten.

  $app  = editApp ( editArg ( $body, 'app' ) );
  $root = editArg ( $body, 'root', 'app' );
  $dir  = trim ( editArg ( $body, 'dir' ), '/' );
  $base = editPath ( $app, $root, $dir );

  if ( ! is_dir ( $base ) )
    editFail ( 'the directory is not there' );

  $files = $_FILES ['files'] ?? NULL;

  if ( ! is_array ( $files ) or ! is_array ( $files ['name'] ?? NULL ) )
    editFail ( 'no files came along' );

  $saved = [];

  foreach ( $files ['name'] as $i => $name ) {

    $error = (int) $files ['error'] [$i];

    if ( $error == UPLOAD_ERR_INI_SIZE or $error == UPLOAD_ERR_FORM_SIZE )
      editFail ( "$name is larger than this server takes: upload_max_filesize is " . ini_get ( 'upload_max_filesize' ) );

    if ( $error != UPLOAD_ERR_OK )
      editFail ( "$name did not arrive (upload error $error)" );

    $name   = editName ( $name );
    $rel    = $dir === '' ? $name : "$dir/$name";
    $target = editPath ( $app, $root, $rel );

    if ( file_exists ( $target ) )
      editFail ( "$rel exists already" );

    if ( ! move_uploaded_file ( $files ['tmp_name'] [$i], $target ) )
      editFail ( "$name could not be stored" );

    @chmod ( $target, 0644 );

    $saved [] = $rel;

  }

  return [ 'saved' => $saved ];

?>
