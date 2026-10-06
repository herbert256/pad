<?php

  // Fixture for the extending page: an application tag that reads a parameter and returns
  // what it renders. The return value is a value - its braces stay text - where $padContent
  // would be template source, so a parameter never goes there.

  $label = padTagParm ( 'label', 'plain' );

  return '&laquo;' . htmlspecialchars ( $label ) . '&raquo;';

?>
