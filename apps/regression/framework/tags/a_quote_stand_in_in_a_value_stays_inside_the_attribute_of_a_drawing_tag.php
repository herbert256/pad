<?php

  // A value holding the stand-in of a quote - what %EE%80%A2 in a request is - written by
  // the tags that draw into an attribute: escaped as the quote it becomes when the page is
  // written out, where their escapers left it and it closed the attribute and opened one
  // of the visitor's own, an event handler.

  $hostile = "red\u{E022} onclick=\u{E022}alert(1)";

?>
