<?php

  // Raises two engine-level PHP warnings - reading undefined variables - which
  // $padErrorLevel promotes to PAD errors handled by this application's error action.
  // Two, because the action promises a dump per error, not only for the first.

  $boom  = $neverSetAnywhere;
  $boom2 = $neverSetAnywhereEither;

?>
