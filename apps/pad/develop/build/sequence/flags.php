<?php

  fileDeleteDir ( "sequence/types/$type/flags/" );
  mkdir     ( "sequence/types/$type/flags/" );

  filePutFile ( "sequence/types/$type/flags/readme.txt", 'This directory is generared' );

?>
