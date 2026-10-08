<?php

  $before = "function total ( \$rows ) {\n  \$sum = 0;\n  foreach ( \$rows as \$row )\n    \$sum += \$row ['price'];\n  return \$sum;\n}";
  $after  = "function total ( \$rows, \$tax = 0.21 ) {\n  \$sum = 0;\n  foreach ( \$rows as \$row )\n    \$sum += \$row ['price'] * \$row ['quantity'];\n  return round ( \$sum * ( 1 + \$tax ), 2 );\n}";

?>
