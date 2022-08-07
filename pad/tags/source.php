<?php

  if ( pTag_parm ('after') and $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;
  }

	$pContent = '{ignore}' . pColors_string ($pContent) . '{/ignore}';

	return TRUE;

?>