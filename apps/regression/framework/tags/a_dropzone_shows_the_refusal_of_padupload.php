<?php

  // padUpload refused the file: the drop zone shows why, in the words of its label, as
  // {input type='file'} does.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f' ];

  padUploadRefuse ( 'cv', ':label is larger than :n', '2 MB' );

?>
