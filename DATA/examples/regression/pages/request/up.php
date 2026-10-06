<?php

  // The upload fixture: what a multipart file field delivers, seen from the page.

  $f = $_FILES ['f'] ?? [];

  echo 'name:' . ( $f ['name'] ?? '-' )
     . ' size:' . ( $f ['size'] ?? '-' )
     . ' uploaded:' . ( isset ( $f ['tmp_name'] ) && is_uploaded_file ( $f ['tmp_name'] ) ? 'yes' : 'no' );

?>
