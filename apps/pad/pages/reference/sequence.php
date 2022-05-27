<?php

  $sequence = '';

  $title = 'Sequence';

  $path = PAD . 'sequence/types/';

  $directory = new DirectoryIterator ($path);
  $iterator  = new IteratorIterator  ($directory);

  foreach ($iterator as $loop_info) {

    $one = $loop_info->getPathname();

    if ( is_dir($one) ) {

      $item = str_replace ( $path, '', $one );
    
      if ( $item and $item <> '.' and $item <> '..' ) {

        $dirs [$item] ['item'] = $item;

        if ($dir)
          $dirs [$item] ['link'] = "$dir/$item";
        else
          $dirs [$item] ['link'] = $item;
  
        if ( count ( pad_dir_list ($one) ) )   
          $dirs [$item] ['next'] = 'dir';
        else
          $dirs [$item] ['next'] = 'index';

      }

    }
 
  }

?>