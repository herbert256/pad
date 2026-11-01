<?php

  // The playground's editor: a template and its data on the left, what they render to on
  // the right. The data is a JSON object or - the button beside its label switches - PHP
  // code, as a page's .php; each has its own pane, with the same sample in it. The form
  // posts to ?render into the output frame, which is sandboxed - the rendered page's own
  // scripts do not run, and it cannot reach the playground or its cookies.
  // www/playground/playground.js keeps the panes in the URL hash, so a link carries the
  // example, switches the data pane, and renders on load and on Ctrl+Enter.

  $sampleSource = "<h2>{\$title}</h2>\n<ul>\n  {items}\n    <li>{\$name}{if \$stock eq 0} - sold out{/if}</li>\n  {/items}\n</ul>\n";

  $sampleData = "{\n  \"title\": \"Fruit\",\n  \"items\": [\n    { \"name\": \"Apple\", \"stock\": 3 },\n    { \"name\": \"Pear\",  \"stock\": 0 }\n  ]\n}\n";

  $samplePhp = "<?php\n\n  \$title = 'Fruit';\n\n  \$items = [\n    [ 'name' => 'Apple', 'stock' => 3 ],\n    [ 'name' => 'Pear',  'stock' => 0 ]\n  ];\n\n?>\n";

?>
