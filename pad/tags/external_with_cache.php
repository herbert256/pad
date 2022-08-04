<?php

  if ( ! isset ($pad_prms_tag ['url']) )
    $pad_prms_tag ['url'] = $pad_parm;

  if ( ! $pad_prms_tag ['url'] )
    return pad_error ("Curl: No URL given");

  $pad_return = pad_curl ( $pad_prms_tag);

  return $pad_return ['data'];

?>