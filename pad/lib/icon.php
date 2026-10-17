<?php

  // padIconNames - the names of the icons of {icon}, in the order of PAD_ICONS - for a
  // page's PHP as well as for the tag: the showcase lists every icon with it. The icons and
  // the rest of the tag are tags/_lib/icon.php, which the call includes when no {icon} of
  // the page has yet.

  function padIconNames () {

    include_once PAD . 'tags/_lib/icon.php';

    return array_keys ( PAD_ICONS );

  }

?>
