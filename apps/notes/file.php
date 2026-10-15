<?php

  // A file of a note, stored under DATA/notes/files/ - a picture, a PDF or text shown, the
  // rest downloaded, everything downloaded with &download (padUploadSend).

  $attachment = db ( "RECORD * FROM attachments WHERE id={0}", [ (int) padRequest ( 'id', 0 ) ] );

  if ( ! $attachment )
    padAbort ( 404, 'There is no such file' );

  padUploadSend ( $attachment ['file'], $attachment ['name'], padRequestHas ( 'download' ) );

?>
