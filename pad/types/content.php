<?php

  // Type handler for stored content ({content:name}, or a bare tag whose name is in the content
  // store): returns the text through get/content.php, which level/go.php has already primed
  // with $padGetName set to the tag name. Content is put there by the toContent option.

  return include PAD . 'get/content.php';

?>