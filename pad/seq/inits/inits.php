<?php

  include 'seq/inits/clear.php';
  include 'seq/inits/vars.php';
  include 'seq/inits/parms.php';
  include 'seq/inits/seq.php';
  include 'seq/inits/options.php';
  include 'seq/inits/operations.php';
  include 'seq/inits/name.php';
  include 'seq/inits/startEnd.php';
  include 'seq/inits/build.php';
  include 'seq/inits/rows.php';

  if ( file_exists ( PAD . "seq/types/$padSeqSeq/init.php" ) ) 
    include "seq/types/$padSeqSeq/init.php";

?>