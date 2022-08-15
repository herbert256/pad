<?php

  $padTagTraceData ['walk_after'] = $padWalk [$pad];
  $padTagTraceData ['result']     = $padTagResult;
  $padTagTraceData ['content']    = $padTagContent;

  padFilePutContents ( $padLevelDir [$pad] . "/tag." . $padTagCnt [$pad] . '.json',  $padTagTraceData );
  
?>