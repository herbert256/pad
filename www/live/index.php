<?php

  // The live application's entry point. A request for ?page/<id> is a page the editor saved
  // under that id (apps/live/_lib/live.php): its template is handed to the engine here, in
  // $padPageSource - which only an entry point sets (pad/inits/page.php) - and the page's
  // directory, apps/live/page/, runs its PHP in its _inits.php. Every other request is the
  // application's own.

  include __DIR__ . '/../../home/home.php';

  if ( preg_match ( '#^page/([0-9a-f]{20})(?:&|$)#', (string) ( $_SERVER ['QUERY_STRING'] ?? '' ), $liveMatch )
       and is_file ( "$padHome/DATA/live/$liveMatch[1].pad" ) )
    $padPageSource = file_get_contents ( "$padHome/DATA/live/$liveMatch[1].pad" );

  unset ( $liveMatch );

  include __DIR__ . '/../pad.php';

?>
