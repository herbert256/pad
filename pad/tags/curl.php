<?php

  if ( ! isset ($pad_parms_tag ['url']) )
    $pad_parms_tag ['url'] = $pad_parm;

  if ( ! $pad_parms_tag ['url'] )
    return pad_error ("Curl: No URL given");

  $pad_return = pad_curl ( $pad_parms_tag);

  return $pad_return ['data'];

?>