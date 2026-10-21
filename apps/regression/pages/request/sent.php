<?php

  // The fixture of upload_send: a file stored under DATA sent back as the page's whole
  // answer with padUploadSend - which file, and how, the request's 'which' says.

  @mkdir ( DATA . 'regression-sent', 0777, TRUE );

  file_put_contents ( DATA . 'regression-sent/note.txt', "Hello from DATA\n" );
  file_put_contents ( DATA . 'regression-sent/page.html', "<!DOCTYPE html><script>document.title = 'ran'</script>\n" );

  // A file larger than the memory the request may take - for a_stored_file_larger_than_the_
  // memory_limit_is_sent_whole: sixteen megabytes under a limit ten above what is in use
  // (finfo alone reads up to seven of a file to tell its type).

  if ( padRequest ( 'which' ) == 'big' ) {
    if ( ! is_file ( DATA . 'regression-sent/big.bin' ) or filesize ( DATA . 'regression-sent/big.bin' ) != 16 << 20 )
      file_put_contents ( DATA . 'regression-sent/big.bin', str_repeat ( 'PAD.', 4 << 20 ) );
    ini_set ( 'memory_limit', (string) ( memory_get_usage ( TRUE ) + ( 10 << 20 ) ) );
  }

  match ( padRequest ( 'which', 'note' ) ) {
    'big'      => padUploadSend ( 'regression-sent/big.bin', 'big.bin', TRUE ),
    'download' => padUploadSend ( 'regression-sent/note.txt', 'My note.txt', TRUE ),
    'html'     => padUploadSend ( 'regression-sent/page.html', 'page.html' ),
    'climb'    => padUploadSend ( 'regression-sent/../../apps/regression/pages/request/sent.php' ),
    default    => padUploadSend ( 'regression-sent/note.txt', 'My note.txt' )
  };

?>
