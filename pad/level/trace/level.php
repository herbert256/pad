<?php
  
  if ( ! $padTrace )
    return;

  pFile_put_contents ( $padLevelDir [$pad] . "/level.json", pTraceGetLevel($pad) ); 
  pFile_put_contents ( $padLevelDir [$pad] . "/base.html",  $padBase [$pad]     );
  pFile_put_contents ( $padLevelDir [$pad] . "/data.json",  $padData [$pad]     );
  
?>