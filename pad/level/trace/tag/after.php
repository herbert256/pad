<?php

  $pTag_trace_data ['walk_after'] = $pWalk [$p];
  $pTag_trace_data ['result']     = $pTagResult [$p];
  $pTag_trace_data ['content']    = $pTagContent;
  $pTag_trace_data ['globals']    = $GLOBALS;

  pFile_put_contents ( $pLevelDir [$p] . "/result." . $pTagCnt [$p] . '.json',  $pTag_trace_data );
  
?>