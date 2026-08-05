<?php

  // {ajax 'page'} embeds another PAD page that the browser fetches for itself;
  // start/ajax.php builds the <div> plus the XMLHttpRequest that fills it, and returns it
  // as the tag's value.

  return include PAD . 'start/ajax.php' ;

?>