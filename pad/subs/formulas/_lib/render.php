<?php

  // A computed sheet as the HTML of {spreadsheet}: a <table> with the column letters as its
  // head and the row numbers as the head of each row - th scope="col" and scope="row", so a
  // screen reader names a cell as a spreadsheet does - unless headers=FALSE. A cell shows its
  // value, a number through its column's format; numbers stand right, TRUE and FALSE and
  // errors in the middle, an error marked (class err) and explained in its title. A formula's
  // cell (class fx) carries the formula in its title and in data-formula, a number it computed
  // in data-value, an error in data-error.
  //
  // The cells to edit are <input>s named padSheet[<sheet>][<cell>], labelled by the cell and
  // the name of its column. Where they post depends on what the sheet stands in ($view
  // ['mode']): in a {form} they go with the form; in a {live} region the sheet is a form the
  // live script posts (pad-submit), which a change of a cell submits - every cell's text
  // once, so the region comes back computed without a script of the page's own; anywhere
  // else the sheet is a form of its own, posting back with a Recalculate button. Each cell
  // of a live sheet carried the texts of all the others in its pad-value: 900 cells wrote
  // 10.7 MB.

  function padSheetHtml ( $sheet, $view ) {

    $name    = $view ['name'];
    $inputs  = $view ['inputs'];
    $html    = '<table class="pad-sheet-table">';

    if ( $view ['caption'] !== '' )
      $html .= '<caption>' . padChartAttr ( $view ['caption'] ) . '</caption>';

    if ( $view ['headers'] ) {
      $html .= '<thead><tr><td class="pad-sheet-corner"></td>';
      for ( $column = 1; $column <= $sheet ['width']; $column++ )
        $html .= '<th scope="col">' . padFormulaLetters ( $column ) . '</th>';
      $html .= '</tr></thead>';
    }

    $html .= '<tbody>';

    for ( $row = 1; $row <= $sheet ['height']; $row++ ) {

      $html .= '<tr>' . ( $view ['headers'] ? "<th scope=\"row\">$row</th>" : '' );

      for ( $column = 1; $column <= $sheet ['width']; $column++ )
        $html .= padSheetCellHtml ( $sheet, $column, $row, $view, isset ( $inputs ["$column:$row"] ) );

      $html .= '</tr>';

    }

    $html .= '</tbody></table>';

    $script = '';

    if ( $view ['mode'] == 'own' and $inputs )
      $html = '<form method="post" class="pad-sheet-form">' . $html
            . '<button type="submit" class="pad-sheet-recalc">' . padChartAttr ( padTransCatalog () ['spreadsheet.recalculate'] ?? 'Recalculate' ) . '</button></form>';

    // In a live region the region's name is the form's first field: the live script adds it
    // last, behind every cell, and a post of more fields than PHP's max_input_vars keeps -
    // 1000 - lost it, and with it the region.

    elseif ( $view ['mode'] == 'live' and $inputs ) {
      $region = preg_match ( '/^[A-Za-z0-9_-]{1,64}$/D', $view ['region'] ?? '' )
              ? '<input type="hidden" name="padLive" value="' . $view ['region'] . '">' : '';
      $html   = '<form method="post" class="pad-sheet-form" pad-submit="sheet-' . padChartAttr ( $name ) . '" data-pad-sheet-live>'
              . $region . $html . '</form>';
      $script = padWidgetScript ( 'sheet-live', padSheetLiveScript () );
    }

    return padSheetStyle () . '<div class="pad-sheet" data-sheet="' . padChartAttr ( $name ) . '">' . $html . '</div>' . $script;

  }

  // The script of a live sheet, once a page: a change of a cell submits the sheet's form,
  // which the live script posts as the region's event - requestSubmit fires the submit event
  // it listens for; a browser without it gets the event alone.

  function padSheetLiveScript () {

    return "(function(){if(window.padSheetLive)return;window.padSheetLive=true;"
         . "document.addEventListener('change',function(e){var f=e.target.form;"
         . "if(!f||!f.hasAttribute('data-pad-sheet-live'))return;"
         . "if(f.requestSubmit)f.requestSubmit();else f.dispatchEvent(new Event('submit',{bubbles:true,cancelable:true}));});})();";

  }

  function padSheetCellHtml ( $sheet, $column, $row, $view, $input ) {

    $cell    = $sheet ['cells'] [$row] [$column] ?? NULL;
    $formula = $cell ['formula'] ?? NULL;
    $value   = $cell ? ( $formula !== NULL ? $cell ['result'] : $cell ['value'] ) : NULL;
    $format  = padSheetFormatAt ( $view ['formats'], $column, $row ) ?? ( $cell ['format'] ?? NULL );
    $shown   = padSheetShow ( $value, $format );
    $classes = [];
    $attrs   = '';

    if     ( is_float ( $value ) or is_int ( $value ) ) $classes [] = 'num';
    elseif ( is_bool ( $value ) )                       $classes [] = 'bool';
    elseif ( padFormulaIsError ( $value ) )             $classes [] = 'err';

    if ( $formula !== NULL ) {

      $classes [] = 'fx';
      $title      = $formula;

      if ( padFormulaIsError ( $value ) )
        $title .= ' - ' . $value ['error'] . ', ' . padSheetWhy ( $cell, $value ['error'] );

      $attrs .= ' title="' . padChartAttr ( $title ) . '" data-formula="' . padChartAttr ( $formula ) . '"';

      if ( is_float ( $value ) or is_int ( $value ) )
        $attrs .= ' data-value="' . padFormulaNumberText ( (float) $value ) . '"';

    } elseif ( padFormulaIsError ( $value ) )
      $attrs .= ' title="' . padChartAttr ( $value ['error'] . ', ' . padFormulaErrorTexts [ $value ['error'] ] ) . '"';

    if ( padFormulaIsError ( $value ) )
      $attrs .= ' data-error="' . padChartAttr ( $value ['error'] ) . '"';

    if ( ! $input ) {
      $class = $classes ? ' class="' . implode ( ' ', $classes ) . '"' : '';
      return "<td$class$attrs>" . padChartAttr ( $shown ) . '</td>';
    }

    // A cell to edit: what was typed in it, and for a formula its value beside it.

    $classes [] = 'in';

    // Its label: the cell, and the texts that head its row (in column A) and its column (in
    // row 1) - B2, Apples, Price.

    $cellName = padFormulaCellName ( $column, $row );
    $label    = $cellName;
    $across   = $sheet ['cells'] [$row] [1] ['value'] ?? NULL;
    $down     = $sheet ['cells'] [1] [$column] ['value'] ?? NULL;

    if ( $column > 1 and is_string ( $across ) and trim ( $across ) !== '' )
      $label .= ', ' . $across;

    if ( $row > 1 and is_string ( $down ) and trim ( $down ) !== '' )
      $label .= ', ' . $down;

    $field = '<input name="padSheet[' . padChartAttr ( $view ['name'] ) . '][' . $cellName . ']"'
           . ' value="' . padChartAttr ( $cell ['source'] ?? '' ) . '"'
           . ' aria-label="' . padChartAttr ( $label ) . '"'
           . ( ( is_float ( $value ) or is_int ( $value ) ) ? ' inputmode="decimal"' : '' )
           . ' autocomplete="off">';

    if ( $formula !== NULL )
      $field .= '<output>' . padChartAttr ( $shown ) . '</output>';

    return '<td class="' . implode ( ' ', $classes ) . '"' . $attrs . '>' . $field . '</td>';

  }

  // Why a formula answers an error, for its title: the circle it stands in, the place its
  // formula cannot be read - whatever error the lenient reading of it answers, #ERROR!, a
  // #NAME? for a function that is not there, #N/A for the wrong number of arguments, #VALUE!
  // for a SUMPRODUCT of an operator on a range - else what the error means.

  function padSheetWhy ( $cell, $error ) {

    if ( isset ( $cell ['circle'] ) )
      return 'a circular reference: ' . $cell ['circle'];

    if ( isset ( $cell ['problem'] ) )
      return $cell ['problem'] [0] . ' (at ' . $cell ['problem'] [1] . ')';

    return padFormulaErrorTexts [$error] ?? '';

  }

  // The style, once a page: its colours are custom properties with light and dark defaults
  // on .pad-sheet - --pad-sheet-text, -muted, -line, -head, -formula, -input, -error and
  // -accent - which a page overrides.

  function padSheetStyle () {

    $css = padWidgetFile ( 'formulas/sheet.css' );

    return padWidgetStyle ( 'sheet', [
      'text'    => [ '#1f1f1d', '#ecebe6' ],
      'muted'   => [ '#6b6a66', '#9d9c93' ],
      'line'    => [ '#dcdbd6', '#3a3a37' ],
      'head'    => [ '#f2f1ed', '#262624' ],
      'formula' => [ '#f7f9fd', '#1d2330' ],
      'input'   => [ '#fffdf2', '#2a2818' ],
      'error'   => [ '#b42318', '#f97066' ],
      'accent'  => [ '#2a78d6', '#5598e7' ]
    ], $css );

  }

?>
