<?php

  // Where a <link> cannot work the text of the files of www/assets/ is written in, as it was
  // everywhere before they were linked: a mail (a mail client fetches no style sheet - the
  // mail rendered during this request, whose page links), an offline application (pad pack,
  // PAD_WASM: no server behind the page), a static copy (pad export) and every output that
  // is not the web's (lib/widget.php padWidgetLinked).

  $padAssetLinks = TRUE;

  include_once PAD . 'tags/_lib/mail.php';

  $mailTpl  = padMailTemplate ( 'assets' );
  $mailHtml = padMailRender ( $mailTpl ['dir'], $mailTpl ['html'], 'pad' );

  $switches = [ 'web' => padWidgetLinked () ];

  $_SERVER ['PAD_WASM'] = '1';
  $switches ['offline'] = padWidgetLinked ();
  unset ( $_SERVER ['PAD_WASM'] );

  $GLOBALS ['padExportRun'] = TRUE;
  $switches ['export'] = padWidgetLinked ();
  $GLOBALS ['padExportRun'] = FALSE;

  $GLOBALS ['padOutputType'] = 'console';
  $switches ['console'] = padWidgetLinked ();
  $GLOBALS ['padOutputType'] = 'web';

  $GLOBALS ['padAssetLinks'] = FALSE;
  $switches ['off'] = padWidgetLinked ();
  $GLOBALS ['padAssetLinks'] = TRUE;

  $switchText = implode ( ' ', array_map ( fn ( $k, $v ) => "$k=" . ( $v ? 'linked' : 'written' ), array_keys ( $switches ), $switches ) );

?>
