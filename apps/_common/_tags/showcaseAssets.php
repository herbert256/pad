<?php
  $demoAssets = '';
  foreach ( [ 'showcase-examples.css' => 'style', 'showcase-examples.js' => 'script' ] as $file => $element ) {
    $at = dirname ( APPS ) . "/www/_common/$file";
    if ( padWasm () )
      $demoAssets .= "<$element>" . file_get_contents ( $at ) . "</$element>";
    else {
      $url = htmlspecialchars ( $padRoot . "_common/$file?v=" . substr ( hash_file ( 'sha256', $at ), 0, 10 ), ENT_QUOTES );
      $demoAssets .= $element == 'style' ? "<link rel=\"stylesheet\" href=\"$url\">" : "<script src=\"$url\" defer></script>";
    }
  }
  return $demoAssets;
?>
