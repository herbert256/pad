<?php

  // Everything Notes holds, to take along or to bring back: the notes as JSON (the export
  // page answers them - $padExpose), the database file itself (the database page), and an
  // import of such a JSON file - its notes added to these, never in their place.

  if ( padPosted ( 'import' ) ) {

    $upload = padUpload ( 'json', types: [ 'application/json', 'text/plain' ], max: '10M', dir: 'notes/imports' );

    if ( $upload ) {

      $data = json_decode ( (string) file_get_contents ( $upload ['path'] ), TRUE );
      @unlink ( $upload ['path'] );

      if ( ! is_array ( $data ['notes'] ?? NULL ) ) {
        padFlash ( 'That file holds no notes - a file the export of this page wrote does.', 'error' );
        padRedirect ( 'backup' );
      }

      $added = 0;

      foreach ( $data ['notes'] as $one ) {

        if ( ! is_array ( $one ) or trim ( (string) ( $one ['title'] ?? '' ) ) === '' )
          continue;

        $stamp = fn ( $key ) => preg_match ( '/^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d$/', (string) ( $one [$key] ?? '' ) ) ? $one [$key] : notesNow ();
        $color = isset ( notesColors () [ $one ['color'] ?? '' ] ) ? $one ['color'] : 'plain';

        $id = db ( "INSERT INTO notes (title, body, color, pinned, created, updated) VALUES ('{0}', '{1}', '{2}', {3}, '{4}', '{5}')",
                   [ mb_substr ( trim ( (string) $one ['title'] ), 0, 120 ), (string) ( $one ['body'] ?? '' ), $color,
                     empty ( $one ['pinned'] ) ? 0 : 1, $stamp ( 'created' ), $stamp ( 'updated' ) ] );

        notesSaveTags ( $id, implode ( ',', array_filter ( (array) ( $one ['tags'] ?? [] ), 'is_string' ) ) );

        $added++;

      }

      padFlash ( $added == 1 ? 'One note imported.' : "$added notes imported." );
      padRedirect ( 'index' );

    }

  }

  $noteCount = (int) db ( "FIELD count(*) FROM notes" );
  $tagCount  = (int) db ( "FIELD count(DISTINCT tag) FROM tags" );
  $fileCount = (int) db ( "FIELD count(*) FROM attachments" );
  $fileBytes = notesSize ( (int) db ( "FIELD coalesce(sum(size), 0) FROM attachments" ) );
  $dbBytes   = notesSize ( @filesize ( DATA . 'notes/notes.sqlite' ) ?: 0 );
  $inBrowser = padWasm ();
  $notesPage = 'Backup';

?>
