<?php
 
  $file = 'array/alt/json1.json';
  $total = $file = APP . "data/$file";

  if ( pad_file_valid_name($total))
    echo "123";
  else
    echo "456";

  $xyz = '';
  $abc = pad_make_data ('array/alt/json1.json');


?>