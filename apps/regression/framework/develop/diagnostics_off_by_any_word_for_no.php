<?php

  // $padDiagnostics switched the full reports off only when it was the boolean FALSE: a 0 in
  // the configuration, or the '0' or 'off' an .env holds when read through padEnv, left
  // them on - behind a proxy on the same machine, for every visitor. {debug} asks the same
  // padLocal () the error report does.

  $padDiagnostics = 0;
  $shownZero      = padLocal () ? 'shown' : 'off';

  $padDiagnostics = 'off';
  $shownWord      = padLocal () ? 'shown' : 'off';

  $padDiagnostics = TRUE;
  $shownOn        = padLocal () ? 'shown' : 'off';

?>
