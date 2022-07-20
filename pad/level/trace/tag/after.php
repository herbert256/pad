<?php

  $pad_tag_trace_data ['walk_after'] = $pad_walk;
  $pad_tag_trace_data ['result']     = $pad_tag_result;

  pad_file_put_contents ( "$pad_trace_dir_lvl/result/" . $pad_tag_cnt [$pad_lvl] . '.json',  $pad_tag_trace_data );
  
?>