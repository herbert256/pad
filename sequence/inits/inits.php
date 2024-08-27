<?php

  include_once '/pad/sequence/inits/_lib.php';

  include '/pad/sequence/inits/clear.php';
  include '/pad/sequence/inits/vars.php';
  include '/pad/sequence/inits/parms.php';
  include '/pad/sequence/inits/sequence.php';
  include '/pad/sequence/inits/operations.php';
  include '/pad/sequence/inits/actions.php';
  include '/pad/sequence/inits/options.php';
  include '/pad/sequence/inits/store.php';
  include '/pad/sequence/inits/name.php';
  include '/pad/sequence/inits/startEnd.php';
  include '/pad/sequence/inits/build.php';
  include '/pad/sequence/inits/rows.php';

  if ( file_exists ( "/pad/sequence/types/$padSeqSeq/init.php" ) ) 
    include "/pad/sequence/types/$padSeqSeq/init.php";

?>