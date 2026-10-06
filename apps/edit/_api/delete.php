<?php

  // Deletes a file or a directory: into the trash, from where it can be put back. What has
  // been in the trash longer than $editTrashDays goes now.

  global $editUser, $editTrashDays;

  [ $app, $root, $rel, $file ] = editTarget ( $body );

  if ( $rel === '' )
    editFail ( 'the root itself is not deleted' );

  $id = editTrashPut ( editStore (), $app, $root, $rel, $file, $editUser );

  editTrashPurge ( editStore (), $editTrashDays );

  return [ 'trash' => $id ];

?>
