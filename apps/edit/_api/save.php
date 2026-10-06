<?php

  // Writes a file. The editor sends the sha1 of the text it opened: when the file on disk
  // is no longer that text, somebody else changed it in the meantime, and nothing is
  // written - the answer is the disk's version, for the editor to show beside its own,
  // unless the save says to force it. The text it replaces goes into the history first.

  global $editUser, $editHistoryKeep, $editMaxText;

  [ $app, $root, $rel, $file ] = editTarget ( $body );

  if ( $rel === '' or is_dir ( $file ) )
    editFail ( 'that is a directory' );

  $text = $body ['text'] ?? NULL;

  if ( ! is_string ( $text ) )
    editFail ( 'there is no text to save' );

  $known = editArg ( $body, 'sha1' );
  $there = is_file ( $file );

  if ( $there and empty ( $body ['force'] ) and $known !== '' and sha1_file ( $file ) !== $known )
    return [ 'conflict' => TRUE, 'disk' => editRead ( $file, $editMaxText ) ];

  if ( $there and ! is_writable ( $file ) )
    editFail ( 'the file is read-only' );

  if ( $there ) {
    $old = (string) file_get_contents ( $file );
    if ( $old !== $text )
      editHistoryPut ( editStore (), $app, $root, $rel, $old, $editUser, $editHistoryKeep );
  }

  editWrite ( $file, $text );

  return [ 'conflict' => FALSE, 'sha1' => sha1 ( $text ), 'mtime' => filemtime ( $file ), 'size' => strlen ( $text ) ];

?>
