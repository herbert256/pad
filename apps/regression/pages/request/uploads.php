<?php

  // padUpload on real multipart posts: a PNG is stored under a random name with its real
  // type, a text file sent as image/png with a .png name is refused for what it is, a file
  // over the limit is refused for its size - each refusal also beside the file field, in
  // its label's words - and a post without the field has nothing to take. Nothing looked
  // at $_FILES before.

  $uploadUrl = $padGoExt . 'request/upload_take&padInclude';
  $uploadPng = APP . 'request/_avatar.png';

  $uploadOk   = padCurl ( [ 'url' => $uploadUrl,
                            'post' => [ 'avatar' => new CURLFile ( $uploadPng, 'image/png', 'me.png' ) ] ] );
  $uploadLiar = padCurl ( [ 'url' => $uploadUrl,
                            'post' => [ 'avatar' => new CURLFile ( APP . 'request/_payload.txt', 'image/png', 'evil.png' ) ] ] );
  $uploadBig  = padCurl ( [ 'url' => $uploadUrl . '&max=10',
                            'post' => [ 'avatar' => new CURLFile ( $uploadPng, 'image/png', 'me.png' ) ] ] );
  $uploadNone = padCurl ( [ 'url' => $uploadUrl, 'post' => [ 'other' => 'value' ] ] );

  $uploadsResult = '';

  foreach ( [ 'png' => $uploadOk, 'liar' => $uploadLiar, 'big' => $uploadBig, 'none' => $uploadNone ] as $uploadName => $uploadCurl )
    $uploadsResult .= "$uploadName: " . $uploadCurl ['result'] . ' ' . trim ( $uploadCurl ['data'] ) . "\n";

?>
