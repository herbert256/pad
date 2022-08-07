<?php

  if ( pTag_parm ('after') and $pWalk == 'start' ) {
    $pWalk = 'end';
    return TRUE;
  }

	$pContent = '{ignore}' . pColors_string ($pContent) . '{/ignore}';

	return TRUE;

?>