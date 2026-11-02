<?php

  // {section:item 'Pro', price='€12', featured} ... {/section:item} - one item of the list
  // section around it: a feature, a plan, a quote, a question, a number, a step, a person, a
  // logo, a picture or a column of the footer. Its first value, its options and what its
  // content renders to are handed to that section, which says what they are - the first value
  // is a plan's name and a question's question, the content a plan's features and a question's
  // answer - and reports an option it has no field for. It renders nothing where it stands; a
  // single {section:item .../} is an item without content. Outside a section that lists items
  // it is reported.
  //
  // An item is collected by subs/sections/section.php, never built here: the file is the kind,
  // so the item is one of the sections there are.

  return '';

?>
