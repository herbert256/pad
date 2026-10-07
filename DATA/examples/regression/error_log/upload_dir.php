<?php

  // padUpload checks the directory it is to store in before it stores anything: under this
  // action, which carries on after an error, a name that is no plain directory name is
  // reported and the file refused. It was reported and then used, so a directory built from
  // a request value - "users/$name" with a name of ../../www/x - put the visitor's file
  // wherever the name pointed.

  $uploadDirCurl = padCurl ( [ 'url'  => $padHost . 'regression/error_log/?upload_dir_take&padInclude',
                               'post' => [ 'f' => new CURLFile ( APP . 'upload_dir_take.pad', 'text/plain', 'x.txt' ) ] ] );

  $uploadDir = $uploadDirCurl ['result'] . ' ' . trim ( $uploadDirCurl ['data'] );

?>
