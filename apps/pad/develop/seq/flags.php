<?php

  deleteDir ( PAD . "seq/types/$type/flags/" ); 
  mkdir     ( PAD . "seq/types/$type/flags/" );

  file_put_contents ( PAD . "seq/types/$type/flags/readme.txt", 'This directory is generared' );

?>