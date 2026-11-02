<?php

  // A model that points back at itself - a post whose comments name the post - is read for
  // the fields the kind maps, never walked whole.

  $ann       = new stdClass;
  $ann->name = 'Ann';
  $ann->self = $ann;

  $json = json_encode ( padJsonLd ( 'person', $ann ), JSON_UNESCAPED_SLASHES );

  $member = [ $ann ];

?>
