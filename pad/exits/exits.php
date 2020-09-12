<?php

  $pad_output = str_replace ( ['&open;', '&close;', '&pipe;', '&eq;'], ['{', '}', '|', '='], $pad_output );
  $pad_output = str_replace ( ['#open#', '#close#', '#pipe#', '#eq#'], ['{', '}', '|', '='], $pad_output );

  if ( count ($pad_sanitize) )
    include PAD_HOME . 'exits/sanitize.php';

  if ( $pad_tidy and ! isset($_REQUEST['pad_include']) )
    include PAD_HOME . 'exits/tidy.php';
  
  $pad_etag = pad_short_md5 ($pad_output);

  $pad_ob = ob_get_clean();
  if ($pad_ob) {
    echo "Illegal output<hr><pre>$pad_ob</pre><hr>";
    pad_exit();
  }

  if ( $pad_track_output )
    pad_track_output ();

  $pad_stop = ( $pad_etag_304 and $pad_client_etag == $pad_etag ) ? 304 : 200;
   
  if ( $pad_client_gzip and $pad_stop == 200 )
    $pad_output = pad_zip($pad_output);

  if ( $pad_cache and $pad_cache_server_age )
    include PAD_HOME . 'exits/cache.php';

  include PAD_HOME . 'exits/stop.php';

?>