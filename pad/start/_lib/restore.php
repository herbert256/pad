<?php

  padRestore ( $padSolSave [$padSolCnt] );

  if ( $padResetPad or $padBackupPad )
    include pad . 'start/_lib/restorePad.php';

  if ( $padResetApp or $padBackupApp )
    include pad . 'start/_lib/restoreApp.php';

?>