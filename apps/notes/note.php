<?php

  // One note: its text as Markdown, its tags, its files - pictures shown, the rest to
  // download - and what can be done with it. The whole sheet is a live region: the pin and
  // the colour are clicks this PHP answers with the region alone. Removing a file and the
  // note are posts that come back here, or go to the list.

  $note = notesNote ( padRequest ( 'id', 0 ) );

  if ( ! $note )
    padAbort ( 404, 'There is no such note' );

  $noteId = (int) $note ['id'];

  if ( padLiveEvent () == 'pin' )
    notesPin ( $noteId );

  if ( padLiveEvent () == 'color' and isset ( notesColors () [ padLiveValue () ] ) )
    db ( "UPDATE notes SET color='{0}' WHERE id={1}", [ padLiveValue (), $noteId ] );

  if ( padLiveEvent () )
    $note = notesNote ( $noteId );

  if ( padPosted ( 'detach' ) ) {
    notesDetach ( padRequest ( 'attachment', 0 ) );
    padFlash ( 'The file is removed.' );
    padRedirect ( 'note', [ 'id' => $noteId ] );
  }

  if ( padPosted ( 'delete' ) ) {
    notesDelete ( $noteId );
    padFlash ( "“{$note ['title']}” is deleted." );
    padRedirect ( 'index' );
  }

  $swatches = [];

  foreach ( notesColors () as $key => $label )
    $swatches [] = [ 'key' => $key, 'label' => $label, 'on' => $key == $note ['color'] ? 'true' : 'false' ];

  $noteTags  = $note ['tags'];
  $noteFiles = $note ['files'];
  $fileCount = count ( $noteFiles );
  $notesPage = $note ['title'];

?>
