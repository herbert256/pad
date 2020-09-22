 <?php

  $path      = PAD_APPS . 'manual/pages/';
  $directory = new RecursiveDirectoryIterator ($path);
  $files     = new RecursiveIteratorIterator  ($directory);

  foreach ($files as $file) {

    if ( substr($file->getPathname(), -10) == 'index.html' ) {

      $item = substr ( str_replace ( $path, '', $file->getPathname() ), 0, -11);

      $items [$item] ['item'] = $item;

    }

  }

  ksort ($items);

?>