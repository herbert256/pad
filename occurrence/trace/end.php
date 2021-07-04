<?php

  pad_trace ("occur/end", "nr=$pad_occur_cnt");
    
  pad_file_put_contents ("$pad_trace_dir_occ/_result.html", $pad_html [$pad_lvl] );

?>