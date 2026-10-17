<?php

  // The notes, pinned first and then the last changed: all of them, those of one tag, or
  // those a search finds in a title, a text or a tag. The list is a live region - the star
  // pins a note, and this PHP answers the click with the region alone - and a fragment,
  // which notes.js asks for while a search is typed.

  $only = trim ( notesText ( 'tag' ) );

  if ( padLiveEvent () == 'pin' )
    notesPin ( (int) padLiveValue () );

  $notes   = notesList ( $search, $only );
  $tagList = notesTags ();
  $total   = (int) db ( "FIELD count(*) FROM notes" );
  $found   = count ( $notes );

  $notesPage = $only !== '' ? "#$only" : ( $search !== '' ? "Search: $search" : 'All notes' );

?>
