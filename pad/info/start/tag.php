<?php

  // Opens a nested reporting scope for a single tag - used by {trace} (pad/tags/trace.php),
  // which switches tracing on for the part of the template it encloses.
  //
  // padInfoBackup (pad/lib/info.php) snapshots every padInfo* global under the current
  // $padInfoCnt, $padInfo is forced on, the counter is raised and padInfoSet re-defaults the
  // switches, so the tag's own config/info/<mode>.php starts from a clean slate.
  // info/end/tag.php undoes all of it.

  include_once PAD . 'info/_lib/_lib.php';

  padInfoBackup ();

  $padInfo = 'go';

  $padInfoCnt++;

  padInfoSet ();

?>