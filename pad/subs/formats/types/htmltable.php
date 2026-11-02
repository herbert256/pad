<?php

  // htmltable - Read-only saved HTML. table selects a zero-based index (default 0) or simple #id; header=first uses unique nonempty first-row text, header=none produces column1… fields. Rowspan/colspan repeat cell text; missing cells are null. Preserves cell whitespace and decoded entities, excluding nested tables/script/style content. HTML parser repairs ordinary HTML omissions; structural span errors report line/table context. No fetching or scripts; external DTD/entity declarations rejected and parser uses NONET. Limits: 4 MiB, 10000 rows, 256 columns, 100000 expanded cells, 8 MiB expanded cell text, rowspan≤1000. extension is htmltable, leaving general HTML handling intact.
  //
  // Runnable example: `apps/tags/examples/format/htmltable.pad`. Independent fixture: `php tests/subsystems/expansion-formats-htmltable.php`.

return ['name'=>'HTML table','mime'=>'text/html','accept'=>[],'extension'=>['htmltable'],'shape'=>'rows','lib'=>'htmltable','raw'=>'exact','options'=>['table','header'],'read'=>'padHtmlTableRead'];
