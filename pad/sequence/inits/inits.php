<?php

  include 'sequence/inits/clear.php';
  include 'sequence/inits/vars.php';
  include 'sequence/inits/parms.php';
  include 'sequence/inits/startEnd.php';
  include 'sequence/inits/sequence.php';
  include 'sequence/inits/options.php';
  include 'sequence/inits/plays.php';
  include 'sequence/inits/name.php';
  include 'sequence/inits/build.php';
  include 'sequence/inits/rows.php';

  if ( file_exists ( "sequence/types/$padSeqSeq/init.php" ) ) 
    include "sequence/types/$padSeqSeq/init.php";

?>