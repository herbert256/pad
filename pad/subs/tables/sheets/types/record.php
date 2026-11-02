<?php
// Same cells and evaluator as grid; labels= maps A1-style cells to application labels.
$padSheetName=trim((string)(is_string($padParm)?$padParm:''));$padSheetRecord=TRUE;
return include PAD.'subs/tables/formulas/spreadsheet.php';
?>
