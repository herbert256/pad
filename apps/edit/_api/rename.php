<?php

  // Renames or moves a file or a directory - within the application, and between its two
  // roots. Its history stays with the old name.

  [ $app, $root, $rel, $from ] = editTarget ( $body );

  if ( $rel === '' )
    editFail ( 'the root itself is not renamed' );

  $toRel = trim ( editArg ( $body, 'to' ), '/' );

  if ( $toRel === '' )
    editFail ( 'the new name is empty' );

  foreach ( explode ( '/', $toRel ) as $part )
    editName ( $part );

  editMove ( $from, editPath ( $app, editToRoot ( $root, editArg ( $body, 'toRoot', $root ) ), $toRel ) );

  return [ 'path' => $toRel ];

?>
