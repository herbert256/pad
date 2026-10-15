<?php

  // padUploadSend answers a stored file as the page: a text shown in the browser, the same
  // file as a download when asked, a page of HTML always downloaded - shown on the site's
  // own address it would run with the site's rights - and a path climbing out of DATA 404.

  $sent = [];

  foreach ( [ 'note', 'download', 'html', 'climb' ] as $which ) {

    $curl = padCurl ( $padGoExt . "request/sent&which=$which" );

    $sent [] = [ 'which'       => $which,
                 'status'      => substr ( $curl ['result'], 0, 3 ),
                 'type'        => $curl ['headers'] ['Content-Type'] ?? '-',
                 'disposition' => $curl ['headers'] ['Content-Disposition'] ?? '-',
                 'body'        => trim ( substr ( (string) $curl ['data'], 0, 60 ) ) ];

  }

?>
