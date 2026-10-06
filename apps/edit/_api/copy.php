<?php

  // Duplicates a file or a directory under a new path, in the same root or the other one.

  [ $app, $root, $rel, $from ] = editTarget ( $body );

  if ( $rel === '' )
    editFail ( 'the root itself is not copied' );

  $to = editPath ( $app, editToRoot ( $root, editArg ( $body, 'toRoot', $root ) ), trim ( editArg ( $body, 'to' ), '/' ) );

  editCopy ( $from, $to );

  return [ 'path' => trim ( editArg ( $body, 'to' ), '/' ) ];

?>
