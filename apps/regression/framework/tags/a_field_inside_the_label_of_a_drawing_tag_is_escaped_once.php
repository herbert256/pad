<?php

  // A field written inside the quoted label of {pad:progress}, {pad:rating}, {icon:star} or the text of
  // {pad:placeholder} is escaped once, as the field alone is: it arrived escaped by its
  // sanitize step and the tag escaped it again - Q&amp;A was shown as Q&amp;amp;A.

  $topic = 'Q&A';

?>
