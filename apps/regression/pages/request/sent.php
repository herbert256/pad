<?php

  // The fixture of upload_send: a file stored under DATA sent back as the page's whole
  // answer with padUploadSend - which file, and how, the request's 'which' says.

  @mkdir ( DATA . 'regression-sent', 0777, TRUE );

  file_put_contents ( DATA . 'regression-sent/note.txt', "Hello from DATA\n" );
  file_put_contents ( DATA . 'regression-sent/page.html', "<!DOCTYPE html><script>document.title = 'ran'</script>\n" );

  match ( padRequest ( 'which', 'note' ) ) {
    'download' => padUploadSend ( 'regression-sent/note.txt', 'My note.txt', TRUE ),
    'html'     => padUploadSend ( 'regression-sent/page.html', 'page.html' ),
    'climb'    => padUploadSend ( 'regression-sent/../../apps/regression/pages/request/sent.php' ),
    default    => padUploadSend ( 'regression-sent/note.txt', 'My note.txt' )
  };

?>
