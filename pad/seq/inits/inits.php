<?php

  include 'seq/inits/clear.php';
  include 'seq/inits/vars.php';
  include 'seq/inits/parms.php';
  include 'seq/inits/options.php';
  include 'seq/inits/sequence.php';
  include 'seq/inits/plays.php';
  include 'seq/inits/name.php';
  include 'seq/inits/start/End.php';
  include 'seq/inits/build.php';
  include 'seq/inits/rows.php';

  if ( file_exists ( "seq/types/$padSeqSeq/init.php" ) ) 
    include "seq/types/$padSeqSeq/init.php";

?>