<?php

  $padTag_trace_data ['walk_after'] = $padWalk [$pad];
  $padTag_trace_data ['result']     = $padTagResult;
  $padTag_trace_data ['content']    = $padTagContent;

  pFile_put_contents ( $padLevelDir [$pad] . "/tag." . $padTagCnt [$pad] . '.json',  $padTag_trace_data );
  
?>