<?php

  include '/pad/sequence/inits/sequence.php';
  include '/pad/sequence/inits/options.php';
  include '/pad/sequence/inits/operations.php';
  include '/pad/sequence/inits/name.php';
  include '/pad/sequence/inits/startEnd.php';
  include '/pad/sequence/inits/build.php';
  include '/pad/sequence/inits/rows.php';

  if ( file_exists ( "/pad/sequence/types/$padSeqSeq/init.php" ) ) 
    include "/pad/sequence/types/$padSeqSeq/init.php";

?>