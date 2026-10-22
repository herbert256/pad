<?php

  // The visitor ticks two boxes of a checkbox list nums[], and the page's PHP only counts
  // them: the list is still the request's, and the store the page pushes under that name
  // takes its place, where reading the name counted as the page making it and the post was
  // refused as "the store name 'nums' is already an array of the page".

  $_GET ['nums'] = [ '1', '5' ];

  padGetParms ( 'GET', [ 'nums' => [ '1', '5' ] ] );

  $picked = count ( $nums ?? [] );

?>
