<?php

  $avatar = padUpload ( 'avatar', types: [ 'image/png', 'image/jpeg' ], max: '2M' );

  $said = $avatar ? 'Stored as ' . $avatar ['file'] : 'No picture was sent yet.';

?>
