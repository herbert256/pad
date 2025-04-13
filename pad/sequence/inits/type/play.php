<?php

  include 'sequence/inits/type/play/action.php';

  include 'sequence/inits/type/play/sequence.php';

  if ( $padSeqPull and $padSeqPlay )
    return;

  include 'sequence/inits/type/play/specials.php';

  if ( $padSeqPull and $padSeqPlay )
    return;

  include 'sequence/inits/type/play/find.php';

?>