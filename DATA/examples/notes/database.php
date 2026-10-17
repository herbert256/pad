<?php

  // The SQLite database itself, downloaded with &download (padUploadSend sends any file
  // under DATA/); without it the page says where it is.

  if ( padRequestHas ( 'download' ) )
    padUploadSend ( 'notes/notes.sqlite', 'notes.sqlite', TRUE );

  $notesPage = 'Database';

?>
