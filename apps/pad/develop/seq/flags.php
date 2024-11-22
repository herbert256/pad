<?php

  deleteDir ( "seq/types/$type/flags/" ); 
  mkdir     ( "seq/types/$type/flags/" );

  file_put_contents ( "seq/types/$type/flags/readme.txt", 'This directory is generared' );

?>