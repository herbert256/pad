<?php

  // A download that names itself report.file and whose body is the name of a data file of
  // this application - what a source that is not to be trusted could answer.

  $padContentType = 'application/octet-stream';

  header ( 'Content-Disposition: attachment; filename="report.file"' );

  echo 'localOnly';

?>
