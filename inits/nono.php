<?php

  $pad_no_no = PAD_APPS . $app . "/pages/$page.php";

  if ( ! file_exists ( $pad_no_no ) )
    pad_boot_error ("Page does not exists: $app/$page");

  foreach ($GLOBALS as $key => $value)
    if ( substr($key, 0, 3) == 'pad' and $key <> 'pad_no_no')
      unset ( $GLOBALS[$key] );

  unset ($page);
  unset ($app);
  unset ($key);
  unset ($value);

  include $pad_no_no;

  exit();

?>