<?php

  $post = 'Crème Brûlée & Co. - a recipe';
  $slug = padStrSlug ( $post );

  $file   = 'shop/orders/list.pad';
  $top    = padStrBefore     ( $file, '/' );
  $folder = padStrBeforeLast ( $file, '/' );
  $base   = padStrAfterLast  ( $file, '/' );

  $heading = padStrBetween ( '<title>Orders</title>', '<title>', '</title>' );

?>
