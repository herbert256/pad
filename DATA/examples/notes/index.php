<?php

  // The notes, pinned first and then the last changed: all of them, those of one tag, or
  // those a search finds in a title, a text or a tag - the tag and the search read by
  // _inits.php, whose menu has the tags. The list is a live region - the star pins a note,
  // and this PHP answers the click with the region alone - and a fragment, which notes.js
  // asks for while a search is typed.

  if ( padLiveEvent () == 'pin' )
    notesPin ( (int) padLiveValue () );

  $notes = notesList ( $search, $only );
  $total = (int) db ( "FIELD count(*) FROM notes" );
  $found = count ( $notes );
  $title = $only !== '' ? "#$only" : ( $search !== '' ? "Found for “{$search}”" : 'All notes' );

?>
