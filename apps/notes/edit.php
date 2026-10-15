<?php

  // A note written or changed: its title, its Markdown, its tags, its colour, a file to
  // attach. The rules stand on the fields (edit.pad) and are checked before this runs - a
  // post that broke them comes back refilled with the messages; one that kept them is
  // stored here, and the page goes to the note. A file refused by its type or size keeps
  // the note from being stored, its reason beside the field.

  $editId = (int) padRequest ( 'id', 0 );
  $draft  = $editId ? notesNote ( $editId ) : NULL;

  if ( $editId and ! $draft )
    padAbort ( 404, 'There is no such note' );

  if ( padPosted ( 'note' ) ) {

    $attach = padUpload ( 'file', types: notesTypes (), max: '8M', dir: 'notes/files' );

    if ( $attach !== FALSE ) {

      $now   = notesNow ();
      $color = isset ( notesColors () [ padRequest ( 'color', '' ) ] ) ? padRequest ( 'color' ) : 'plain';

      if ( $draft )
        db ( "UPDATE notes SET title='{0}', body='{1}', color='{2}', updated='{3}' WHERE id={4}",
             [ padRequest ( 'title' ), padRequest ( 'body', '' ), $color, $now, $editId ] );
      else
        $editId = db ( "INSERT INTO notes (title, body, color, created, updated) VALUES ('{0}', '{1}', '{2}', '{3}', '{3}')",
                       [ padRequest ( 'title' ), padRequest ( 'body', '' ), $color, $now ] );

      notesSaveTags ( $editId, padRequest ( 'tags', '' ) );

      if ( $attach )
        db ( "INSERT INTO attachments (note_id, name, file, type, size, created) VALUES ({0}, '{1}', '{2}', '{3}', {4}, '{5}')",
             [ $editId, $attach ['name'], $attach ['file'], $attach ['type'], $attach ['size'], $now ] );

      padFlash ( $draft ? 'Saved.' : 'Your note is written.' );
      padRedirect ( 'note', [ 'id' => $editId ] );

    }

  }

  $draft     = $draft ?: [ 'id' => 0, 'title' => '', 'body' => '', 'color' => 'plain', 'tags' => [], 'files' => [] ];
  $draftTags = implode ( ', ', array_column ( $draft ['tags'], 'tag' ) );
  $current   = padRequest ( 'color', $draft ['color'] );
  $choices   = [];

  foreach ( notesColors () as $key => $label )
    $choices [] = [ 'key' => $key, 'label' => $label, 'chosen' => $key == $current ];

  $leave     = $draft ['id'] ? "note&id={$draft ['id']}" : 'index';
  $notesPage = $draft ['id'] ? "Edit: {$draft ['title']}" : 'New note';

?>
