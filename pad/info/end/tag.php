<?php

  // Closes the per-tag reporting scope opened by info/start/tag.php: drops $padInfoCnt back
  // and lets padInfoRestore (pad/lib/info.php) put the snapshot of the padInfo* switches taken
  // there back in place, so the settings of a {trace} tag do not leak into the rest of the page.

  $padInfoCnt--;

  padInfoRestore ();

?>