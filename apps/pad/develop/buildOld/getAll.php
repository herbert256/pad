<?php

  fileDeleteDir  ( DAT . '_xref'       );
  fileDeleteDir  ( DAT . '_regression' );

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    $curl = getPage ( $item, 1, 1 );

    filePutFile ( "_regression/$item.html", $curl ['data'] ?? '' );
    filePutFile ( "_regression/$item.txt",  'ok'                 );

  }

?>