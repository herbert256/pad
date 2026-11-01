<?php

  $tagAbout   = 'A grid of cells whose spreadsheet formulas are computed on the server, as a table - formats, totals and cells to edit.';
  $tagGroup   = 'widgets';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{spreadsheet 'name'} Item,Price ... Tea,=B2*2 {/spreadsheet}
{spreadsheet 'name', data='rows', formulas='total=B2*C2', totals='total'/}
{spreadsheet 'name', formats='B:0.00, D:€ #,##0.00', headers=FALSE, caption='Caption'/}
{spreadsheet 'name', editable='B2:C9', seed=7} ... {/spreadsheet}
PAD;

  $tagParms   = [
    'name' => 'The sheet\'s name: its edited cells post as <code>padSheet[name][B2]</code>. Unnamed, a sheet is sheet1, sheet2 ... in the order of the page.' ];

  $tagOptions = [
    'data'     => 'The cells: records under a first row of their field names - their values are never formulas - a list of rows of values, or cells by name. As a pair the content is the cells, a line a row, a comma between cells.',
    'formulas' => 'Formulas put in: <code>total=B2*C2</code> fills a column down every row below the first, <code>D2:D9=B2*C2</code> a range, <code>F1=SUM(D:D)</code> one cell.',
    'totals'   => 'A row below the last with the SUM of each column named - its letters or the name in its first row.',
    'formats'  => 'A format code per column, cell or range: <code>B:0.00, total:€ #,##0.00, B5:B8:0.0%</code>, as TEXT writes them.',
    'headers'  => 'The column letters and the row numbers; <code>headers=FALSE</code> leaves them out.',
    'caption'  => 'The table\'s <code>&lt;caption&gt;</code>.',
    'editable' => 'Cells to edit: bare, every number, date and logical value; or the cells and ranges named. An edit posts back and the sheet is computed again.',
    'seed'     => 'The seed of RAND, 0 when not given: the same numbers on every request.' ];

  $tagSee     = [ 'datatable', 'live', 'form' ];

?>
