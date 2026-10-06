<?php

  // A file field the visitor sends as a list - avatar[] where the page takes one avatar - is
  // refused like any other file the page cannot take, with its message beside the field:
  // it was reported as the author's mistake, and any visitor could turn the page into an
  // error report by renaming the field.

  $listCurl = padCurl ( [ 'url'  => $padGoExt . 'request/upload_take&padInclude',
                          'post' => [ 'avatar[0]' => new CURLFile ( APP . 'request/_avatar.png', 'image/png', 'me.png' ) ] ] );

  $listResult = $listCurl ['result'] . ' ' . trim ( $listCurl ['data'] );

?>
