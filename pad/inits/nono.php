<?php

  if ( ! $pNo_no )
    return; 

  ob_get_clean();

  $pNo_no = PAD . "$app/pages/$page.php";

  if ( ! file_exists ( $pNo_no ) )
    pBoot_error ("Page does not exists: $app/$page");

  foreach ($GLOBALS as $key => $value)
    if ( substr($key, 0, 3) == 'pad' and $key <> 'pNo_no')
      unset ( $GLOBALS[$key] );

  unset ($page);
  unset ($app);
  unset ($key);
  unset ($value);

  unset($PADSESSID);
  unset($PADREFID);
  unset($PADREQID);

  include $pNo_no;

  $pNo_boot_shutdown = TRUE;

  exit();

?>