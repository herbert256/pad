<?php

  // Three error reports on two pages of an application: the console counted the page
  // directories, two.

  $dumpPid    = getmypid ();
  $dumpBefore = adminAppInfo ( 'hello' ) ['dumps'];
  $dumpAll    = count ( adminDumpReports () );

  foreach ( [ "page$dumpPid/aaa", "page$dumpPid/bbb", "other$dumpPid/ccc" ] as $dumpOne ) {
    mkdir ( DATA . "dumps/hello/$dumpOne", 0777, TRUE );
    file_put_contents ( DATA . "dumps/hello/$dumpOne/_ERROR.html", 'boom' );
  }

  $dumpAdded    = adminAppInfo ( 'hello' ) ['dumps'] - $dumpBefore;
  $dumpAllAdded = count ( adminDumpReports () ) - $dumpAll;

  adminRemove ( DATA . "dumps/hello/page$dumpPid" );
  adminRemove ( DATA . "dumps/hello/other$dumpPid" );

  @rmdir ( DATA . 'dumps/hello' );

?>
