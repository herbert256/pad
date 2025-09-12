<?php

  deleteDir ( "sequence/types/$type/flags/" ); 
  mkdir     ( "sequence/types/$type/flags/" );

  file_put_contents ( "sequence/types/$type/flags/readme.txt", 'This directory is generared' );

?>