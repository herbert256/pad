<?php

  // All the notes with their tags, as JSON - ?export&padFormat=json, which the backup page
  // links to and its import reads back. Asked for as a page, it says so.

  $notes = db ( "ARRAY id, title, body, color, pinned, created, updated FROM notes ORDER BY id" ) ?: [];

  foreach ( $notes as &$one )
    $one ['tags'] = array_column ( db ( "ARRAY tag FROM tags WHERE note_id={0} ORDER BY tag", [ $one ['id'] ] ) ?: [], 'tag' );

  unset ( $one );

  $notes     = array_values ( $notes );        // a list - db() keys the rows by their first column
  $padExpose = [ 'notes' ];
  $notesPage = 'Export';

?>
