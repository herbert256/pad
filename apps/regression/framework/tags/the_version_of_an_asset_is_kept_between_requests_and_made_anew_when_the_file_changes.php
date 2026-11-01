<?php

  // The version in the address of a file of www/assets/ - ten hex digits of a hash of its
  // contents - is kept between requests with the file's inode, times and size, as lib/dirs.php
  // keeps a listing (DATA/cache/assets/hashes.json, padWidgetHash): a page that links the file
  // makes one stat of it, where every request read the file and hashed it. A kept hash whose
  // stat matches is the answer, the file not read - here one put in the store by hand; one
  // whose stat no longer matches, the file changed since, is made anew from the file and kept.

  $assetFile = dirname ( PAD ) . '/www/assets/widgets/tabs.css';
  $assetReal = substr ( hash ( 'xxh128', file_get_contents ( $assetFile ) ), 0, 10 );
  $assetStat = stat ( $assetFile );

  $assetStore = & padWidgetHashes ();
  $assetWas   = $assetStore ['widgets/tabs.css'] ?? NULL;

  $assetStore ['widgets/tabs.css'] = [ $assetStat ['ino'], $assetStat ['mtime'], $assetStat ['ctime'], $assetStat ['size'], 'kept000000' ];
  $assetKept = padWidgetUrl ( 'widgets/tabs.css' );

  $assetStore ['widgets/tabs.css'] = [ $assetStat ['ino'], $assetStat ['mtime'] - 60, $assetStat ['ctime'], $assetStat ['size'], 'kept000000' ];
  $assetAnew   = padWidgetUrl ( 'widgets/tabs.css' );
  $assetStored = $assetStore ['widgets/tabs.css'] [4] ?? '';

  // The hand-made hash never goes to disk.

  if ( ( $assetStore ['widgets/tabs.css'] [4] ?? '' ) !== $assetReal ) {
    if ( $assetWas === NULL )
      unset ( $assetStore ['widgets/tabs.css'] );
    else
      $assetStore ['widgets/tabs.css'] = $assetWas;
  }

?>
