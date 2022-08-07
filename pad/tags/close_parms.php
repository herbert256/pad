<?php

  if ( $pWalk == 'start' ) {
    $pWalk = 'end';
    return TRUE;
  }

  $pHtml[$pad-1] = str_replace('###%%%close_parms%%%###', $pContent, $pHtml[$pad-1]);

  $pContent = '';
  
?>