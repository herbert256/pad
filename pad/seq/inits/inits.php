<?php

  include PAD . 'seq/inits/clear.php';
  include PAD . 'seq/inits/vars.php';
  include PAD . 'seq/inits/parms.php';
  include PAD . 'seq/inits/seq.php';
  include PAD . 'seq/inits/options.php';
  include PAD . 'seq/inits/operations.php';
  include PAD . 'seq/inits/name.php';
  include PAD . 'seq/inits/startEnd.php';
  include PAD . 'seq/inits/build.php';
  include PAD . 'seq/inits/rows.php';

  if ( file_exists ( PAD . "seq/types/$padSeqSeq/init.php" ) ) 
    include PAD . "seq/types/$padSeqSeq/init.php";

?>