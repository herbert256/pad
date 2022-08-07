<?php

  $pTag_trace_data ['walk_after'] = $pWalk [$p];
  $pTag_trace_data ['result']     = $pTagResult;
  $pTag_trace_data ['content']    = $pTagContent;

  pFile_put_contents ( $pLevelDir [$p] . "/result." . $TagCnt [$p] . '.json',  $pTag_trace_data );
  
?>