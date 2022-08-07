<?php

  $pTag_trace_data ['walk_after'] = $pad_walk;
  $pTag_trace_data ['result']     = $pTag_result;
  $pTag_trace_data ['content']    = $pTag_content;

  pFile_put_contents ( "$pLevel_dir/result." . $pParms [$pad] ['tag_cnt'] . '.json',  $pTag_trace_data );
  
?>