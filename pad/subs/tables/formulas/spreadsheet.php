<?php

  // The body of {sheet:grid} - pad/tags/spreadsheet.php names it, $padSheetName the name
  // the caller found. A grid of cells, its formulas computed on the server, as a table:
  //
  //   {sheet:grid 'budget', formats='B:€ #,##0.00, D:€ #,##0.00', totals='D'}
  //     Item,Price,Qty,Total
  //     Apples,1.20,3,=B2*C2
  //     Pears,0.80,5,=B3*C3
  //   {/sheet:grid}
  //
  //   {sheet:grid data='orders', formulas='total=B2*C2'/}
  //
  // The cells are the pair's content - a line a row, a comma between cells (a tab when a
  // line holds one), see padSheetRead - or the rows of data=: a {format:auto} store, a page array or
  // a _data file. Records come under a first row of their field names and their values are
  // never formulas; a list of rows of values, or cells by their names ([ 'A1' => 10, 'B1' =>
  // '=A1*2' ]), may hold formulas as the content does. The content is read as written, as
  // {table:standard}'s is: tags in it do not run, a cell holding {$x} holds that text.
  //
  //   formulas=  formulas put in cells: total=B2*C2 fills a column - named in the first row,
  //              a new one when none is - down every row below the first, the formula being
  //              row 2's and its relative references moving with it; D2:D9=B2*C2 fills a
  //              range; F1=SUM(D:D) one cell
  //   totals=    a row below the last with the SUM of the columns it names
  //   formats=   a format code per column, cell or range - B:0.00, total:€ #,##0.00,
  //              B5:0.0%, B5:B8:yyyy-mm-dd, the later deciding where two meet - see
  //              padFormulaFormat; a cell without one shows what it was typed as (12% a
  //              percentage, 2024-01-15 a date), a formula as its first cell, else General
  //   headers    the column letters and the row numbers - on unless headers=FALSE
  //   caption=   the table's caption
  //   editable   cells to edit: alone, every cell holding a number, a date or TRUE/FALSE;
  //              editable='B2:C9, F1' the cells named, formulas too, which show their value
  //              under their text. An edit posts back and the sheet is computed again on the
  //              server - in a {input:form} with it, in a {pad:live} region on every change (the sheet
  //              is a form there, which a change submits), elsewhere as a form of its own
  //              with a Recalculate button. What is posted is typed
  //              into the cell as into a spreadsheet: a formula too, never more than the
  //              cells named, never more than 1000 characters a cell.
  //   seed=      the seed of RAND, 0 unless given
  //
  // The first parameter names the sheet: its edits post as padSheet[<name>][B2]. Unnamed, a
  // sheet is sheet1, sheet2 ... in the order of the page.
  //
  // The tag is {sheet:grid}, not {array:sheet}: a built-in tag comes before a page's fields, and
  // the cheat sheets of the tags and types applications iterate an array named $sheet.
  //
  // Under $padCheckSyntax a formula of the template that cannot be read - or calls a function
  // that is no file of subs/tables/formulas/types/, or with the wrong number of arguments - is an
  // error naming its cell and its place in the formula, as a wrong option is. Without the
  // check, and for a formula a visitor posted, the cell shows #ERROR!, #NAME?, #N/A or
  // #VALUE! (a SUMPRODUCT asking for array evaluation), the reason in its title.

  include_once PAD . 'subs/tables/formulas/_lib/options.php';
  include_once PAD . 'subs/tables/formulas/_lib/render.php';

  padFormulaLoad ();

  $padSheetWhat = ( $padSheetName === '' ) ? 'the spreadsheet' : "the spreadsheet '" . padMakeSafe ( $padSheetName, 40 ) . "'";

  if ( $padSheetName === '' )
    $padSheetName = padSheetNextName ();
  elseif ( ! preg_match ( '/^[A-Za-z][A-Za-z0-9_-]{0,57}$/D', $padSheetName ) ) {
    if ( $padCheckSyntax )
      padError ( "a spreadsheet's name is a letter and then letters, digits, _ and - (58 at most), like {sheet:grid 'budget'}, not '" . padMakeSafe ( $padSheetName, 40 ) . "'" );
    $padSheetName = padSheetNextName ();
  }

  // The cells, from data= or from the content - the level iterates neither: the table is
  // written once.

  $padSheetSource = (string) $padContent;
  $padContent     = '';

  if ( isset ( $padPrm [$pad] ['data'] ) ) {

    if ( trim ( $padSheetSource ) !== '' and $padCheckSyntax )
      padError ( "$padSheetWhat has both data= and cells between its tags - one of the two" );

    $padSheetGrid = padSheetFromData ( $padData [$pad] );

  } elseif ( trim ( $padSheetSource ) !== '' )

    $padSheetGrid = padSheetFromRows ( padSheetRead ( $padSheetSource ) );

  else {

    if ( $padCheckSyntax )
      padError ( "$padSheetWhat has no cells - data='rows', or the rows between {sheet:grid} and {/sheet:grid}" );

    $padSheetGrid = padSheetNew ();

  }

  $padData [$pad] = padDefaultData ();

  if ( ( $padSheetOption = padTagParm ( 'formulas' ) ) !== '' )
    padSheetFormulas ( $padSheetGrid, (string) $padSheetOption, $padSheetWhat );

  if ( ( $padSheetOption = padTagParm ( 'totals' ) ) !== '' )
    padSheetTotals ( $padSheetGrid, (string) $padSheetOption, $padSheetWhat );

  $padSheetFormats = ( ( $padSheetOption = padTagParm ( 'formats' ) ) !== '' ) ? padSheetFormats ( $padSheetGrid, (string) $padSheetOption, $padSheetWhat ) : [];

  $padSheetHeaders = padSheetSwitch ( padTagParm ( 'headers', TRUE ), 'headers', $padSheetWhat );

  $padSheetSeed = padTagParm ( 'seed', 0 );

  if ( ! is_numeric ( $padSheetSeed ) ) {
    if ( $padCheckSyntax )
      padError ( "the seed= of $padSheetWhat is a number, not '" . padMakeSafe ( $padSheetSeed, 20 ) . "'" );
    $padSheetSeed = 0;
  }

  // The cells to edit, and what was posted for them: the fields of the form they stand in.

  $padSheetEditable = padTagParm ( 'editable', FALSE );
  $padSheetInputs   = [];

  if ( $padSheetEditable === TRUE ) {
    foreach ( $padSheetGrid ['cells'] as $padSheetRow => $padSheetCells )
      foreach ( $padSheetCells as $padSheetColumn => $padSheetCell )
        if ( $padSheetCell ['formula'] === NULL and ( is_float ( $padSheetCell ['value'] ) or is_bool ( $padSheetCell ['value'] ) ) )
          $padSheetInputs ["$padSheetColumn:$padSheetRow"] = TRUE;
  } elseif ( $padSheetEditable !== FALSE and $padSheetEditable !== '' and $padSheetEditable !== NULL )
    $padSheetInputs = padSheetCells ( $padSheetGrid, (string) $padSheetEditable, $padSheetWhat );

  [ $padSheetMode, $padSheetRegion ] = padSheetMode ();

  if ( $padSheetInputs )
    padSheetPosted ( $padSheetGrid, $padSheetName, $padSheetInputs );

  padSheetCompute ( $padSheetGrid, (int) $padSheetSeed );

  // A formula of the template that cannot be read is the author's to know.

  if ( $padCheckSyntax )
    foreach ( $padSheetGrid ['cells'] as $padSheetRow => $padSheetCells )
      foreach ( $padSheetCells as $padSheetColumn => $padSheetCell )
        if ( isset ( $padSheetCell ['problem'] ) and empty ( $padSheetCell ['posted'] ) )
          padError ( "the formula " . padMakeSafe ( $padSheetCell ['formula'], 60 ) . " in " . padFormulaCellName ( $padSheetColumn, $padSheetRow )
                   . " of $padSheetWhat cannot be read at position {$padSheetCell ['problem'] [1]}: {$padSheetCell ['problem'] [0]}" );

  // The functions the sheet calls go on the reference record, as the views of {map:auto} do.

  if ( $padInfoXref ?? FALSE ) {
    $padSheetCalls = [];
    foreach ( $padSheetGrid ['cells'] as $padSheetCells )
      foreach ( $padSheetCells as $padSheetCell )
        if ( isset ( $padSheetCell ['tree'] ) )
          padFormulaCalls ( $padSheetCell ['tree'], $padSheetCalls );
    foreach ( array_keys ( $padSheetCalls ) as $padSheetCall )
      padInfoXref ( 'formula', $padSheetCall );
  }

  return padSheetHtml ( $padSheetGrid, [
    'record' => !empty($padSheetRecord),
    'labels' => !empty($padSheetRecord)?padTagParm('labels',[]):[],
    'name'    => $padSheetName,
    'caption' => (string) padTagParm ( 'caption' ),
    'headers' => $padSheetHeaders,
    'formats' => $padSheetFormats,
    'inputs'  => $padSheetInputs,
    'mode'    => $padSheetMode,
    'region'  => $padSheetRegion
  ] );

?>
