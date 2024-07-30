<?php

  if ( $padResetPad or $padBackupPad )
    include pad . 'start/_lib/backupPad.php';

  if ( $padResetApp or $padBackupApp )
    include pad . 'start/_lib/backupApp.php';

  $padSolSave [$padSolCnt] = padSave ();
  
?>