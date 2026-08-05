<?php

  // Settings for $padOutputType 'download', where the rendered page is sent to the browser
  // as an attachment by exits/output/download.php. Adds the Content-Type for the download
  // and then borrows file.php's naming settings, since the suggested file name is built the
  // same way.

  $padContentType = "text/html; charset=UTF-8";

  include PAD . 'config/output/file.php';

?>