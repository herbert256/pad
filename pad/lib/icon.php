<?php

  // padIconNames - the names of the icons of {icon:star}, sorted: the files of subs/icons/types/, the icon
  // subsystem's kinds (padKinds, lib/subsystem.php) - for a page's PHP as well as for the tag:
  // the showcase lists every icon with it. The drawing of the icons and the rest of the tag
  // are subs/icons/_lib/icon.php, which {icon:star} includes.

  function padIconNames () {

    return padKinds ( 'icons' );

  }

?>
