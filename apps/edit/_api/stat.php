<?php

  // Whether open files changed on disk: sha1 and mtime of each. With no files it is the
  // keepalive the editor sends while it is on screen.

  $app  = editArg ( $body, 'app' );
  $list = [];

  foreach ( (array) ( $body ['files'] ?? [] ) as $one ) {

    if ( ! is_array ( $one ) )
      continue;

    $file = editPath ( editApp ( $app ), (string) ( $one ['root'] ?? 'app' ), (string) ( $one ['path'] ?? '' ) );

    $list [] = [ 'root'   => (string) ( $one ['root'] ?? 'app' ),
                 'path'   => (string) ( $one ['path'] ?? '' ),
                 'exists' => is_file ( $file ),
                 'sha1'   => is_file ( $file ) ? sha1_file ( $file ) : '',
                 'mtime'  => is_file ( $file ) ? filemtime ( $file ) : 0 ];

  }

  return $list;

?>
