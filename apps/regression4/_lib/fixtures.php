<?php

  // The harness cases test the regression application's own runner, and several case
  // groups read the fixture globals that runner's library sets at include time - so the
  // library is included here as well, functions and fixtures both. It lives in the
  // regression application because that is whose behaviour the harness group asserts.

  include_once APPS . 'regression/_lib/regression.php';
  include_once APPS . 'regression/_lib/returns.php';

  // The coverage-pattern cases call the reference matchers, which live with the shared
  // application's library - included by path, since _common itself is switched off here.

  include_once APPS . '_common/_lib/reference.php';

?>
