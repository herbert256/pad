<?php

  if ( pTag_parm ('after') and $pWalk [$p] == 'start' ) {
    $pWalk [$p] = 'end';
    return TRUE;
  }

	$pContent = '{ignore}' . pColors_string ($pContent) . '{/ignore}';

	return TRUE;

?>