<?php

  $padSelect ['recent']   = [ 'base' => 'albums', 'where' => 'released >= 2010', 'order' => 'released desc' ];
  $padSelect ['bargains'] = [ 'base' => 'recent', 'where' => 'price < 14' ];

?>
