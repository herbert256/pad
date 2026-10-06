<?php

  // A name taken from the request - an export written as padFilePut ( "exports/$name", ... )
  // - must not make a page the web server runs, under a DATA that lies in the docroot.

  padFilePut ( 'temp/put/report.phtml', 'x' );

?>
