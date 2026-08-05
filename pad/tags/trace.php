<?php

  // The {trace} tag: switches the execution trace on for just the part of the page it wraps,
  // instead of for the whole request as $padInfo = 'trace' would.
  //
  // Like {tidy} it runs twice, driven by $padWalk. On the way in it parks the current info
  // settings (info/start/tag.php), loads the trace flag set (config/info/trace.php) and opens
  // a trace scope; on the way out it closes that scope and restores what was parked, so
  // nothing outside the tag is traced.

  if ( $padWalk [$pad] == 'start' ) {

    include PAD . 'info/start/tag.php';
    include PAD . 'config/info/trace.php';
    include PAD . 'info/types/trace/start.php';

    $padWalk [$pad] = 'end';

  } else {

    include PAD . 'info/types/trace/end.php';
    include PAD . 'info/end/tag.php';

  }

  return TRUE;

?>