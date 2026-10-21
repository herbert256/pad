<?php

  // On Windows the last part of a name loses a trailing blank or dot, and ::$DATA names the
  // file's main stream: report.php followed by a blank is the file report.php there, a page
  // the web server runs - and it was written.

  padFilePut ( 'temp/put/report.php ', 'x' );

?>
