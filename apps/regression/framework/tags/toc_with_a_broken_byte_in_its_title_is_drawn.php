<?php

  // A broken UTF-8 byte in a {toc}'s title - a visitor's value - is a stand-in character, as
  // {$t} shows it, where the JSON of the marker's options was nothing: the request ended,
  // and once the marker was sealed its digits stood in the page as text.

  $t = "Contents \xFF";

?>
