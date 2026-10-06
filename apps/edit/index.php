<?php

  // The editor's page: the frame the browser fills (www/edit/edit.js) and what it needs to
  // start - the applications are asked for with the first call. The user and the token
  // travel in the page; the limits are this server's own, so an upload too large for PHP is
  // refused in the browser before it is sent.

  $editBoot = [
    'user'      => (string) $editUser,
    'monaco'    => $editMonaco,
    'host'      => $padHost,
    'remote'    => (bool) $editRemote,
    'trashDays' => $editTrashDays,
    'home'      => editHome (),
    'terminal'  => (bool) $editTerminal,
    'debug'     => (bool) $editDebug,
    'engine'    => editEngine (),
    'root'      => $padRoot,
    'limits'    => [ 'upload' => editIniBytes ( 'upload_max_filesize' ),
                     'post'   => editIniBytes ( 'post_max_size' ),
                     'text'   => $editMaxText ]
  ];

?>
