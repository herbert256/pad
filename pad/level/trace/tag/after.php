<?php

  $pad_tag_trace_data ['walk_after'] = $pad_walk;
  $pad_tag_trace_data ['result']     = $pad_tag_result;
  $pad_tag_trace_data ['content']    = $pad_tag_content;

  pad_file_put_contents ( "$pad_level_dir/result." . $pad_parms [$pad] ['tag_cnt'] . '.json',  $pad_tag_trace_data );
  
?>