<?php

  $pad_tag_trace_data ['walk_after'] = $pad_walk;
  $pad_tag_trace_data ['result']     = $pad_tag_result;
  $pad_tag_trace_data ['content']    = $pad_tag_content;

  pad_file_put_contents ( "$pad_trace_dir_lvl/result/" . $pad_parameters [$pad_lvl] ['tag_cnt'] . '.json',  $pad_tag_trace_data );
  
?>