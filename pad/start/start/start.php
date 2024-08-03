<?php

  $padStrSto = ['padTables','padRelations','padDataStore','padContentStore','padFlagStore','padSeqStore'];

  $padStrCnt++;

  if ( $padStrCln or $padStrRes )
    include pad . 'start/start/startStores.php';
  
  if ( $padStrCln ) {
    include pad . 'start/start/startCleanPad.php';
    include pad . 'start/start/startCleanApp.php';
  }

  if ( $padStrRes ) {
    include pad . 'start/start/startResetPad.php';
    include pad . 'start/start/startResetApp.php';
  }

  include pad . 'start/start/startLevel.php';
  include pad . 'start/start/startStart.php';

?>