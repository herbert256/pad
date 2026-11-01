<?php

  // The sheets application: a few spreadsheets made with {spreadsheet}, each a page that runs
  // its template and shows it, and a page of every function of the formulas subsystem.
  //
  //   sheetsExamples  the example pages, in the order of the menu: name, title, lead
  //   sheetsShow      what an example page shows - sets $lead, $template, $source, $play and,
  //                   for an example whose cells come from PHP, $phpSource
  //   sheetsFunctions every function as its file of pad/subs/formulas/types/ says it: the
  //                   comment at its top - its syntax and what it does - its group and its
  //                   examples, each computed on the sample sheet as a cell beside it
  //   sheetsSample    the sample sheet the examples of the functions read, _data/sample.csv

  function sheetsExamples () {

    return [
      'budget'   => [ 'Budget',          'A month of a household: amounts by item, their share of the total, SUMIF adding them up by category, and the largest item found with INDEX and MATCH.' ],
      'loan'     => [ 'Loan',            'A loan calculator in a form: change the amount, the interest or the years and recalculate - PMT for the monthly payment and FV for the balance at the end of every year, the years past the term left empty with IF.' ],
      'grades'   => [ 'Grades',          'Test scores averaged, graded with VLOOKUP on a table of the lowest score of each grade, ranked with RANK, counted with COUNTIF, and a student looked up by name with XLOOKUP.' ],
      'dates'    => [ 'Dates',           'Invoices and their due dates: days added to a date, the weekday of the due date with TEXT and WEEKDAY, a due date in the weekend moved to the Monday, the end of the month with EOMONTH, a reminder a month later with EDATE and the days late with DAYS.' ],
      'editable' => [ 'Editable',        'A shopping list in a live region: change a quantity, a price or the VAT and the sheet is computed again on the server and swapped in - no JavaScript of the page\'s own.' ]
    ];

  }

  function sheetsShow ( $name ) {

    global $lead, $template, $source, $play, $phpSource;

    include_once COMMON . '_lib/playground.php';

    $lead      = sheetsExamples () [$name] [1];
    $template  = trim ( (string) file_get_contents ( APP . "_sheets/$name.pad" ) );
    $source    = padHighlightTokens ( $template, 'pad' );
    $php       = is_file ( APP . "_sheets/$name.php" ) ? trim ( (string) file_get_contents ( APP . "_sheets/$name.php" ) ) : '';
    $phpSource = $php !== '' ? padHighlightTokens ( $php, 'php' ) : '';
    $play      = padPlayLink ( "$template\n", '', $php !== '' ? "$php\n" : '' );

  }

  function sheetsSample () {

    return (string) file_get_contents ( APP . '_data/sample.csv' );

  }

  // The examples of the comment of a function's file: the lines that start with a formula, the
  // value it shows after two spaces or more.

  function sheetsFunctions () {

    padFormulaLoad ();

    $functions = [];

    foreach ( padKinds ( 'formulas' ) as $name ) {

      $file = PAD . "subs/formulas/types/$name.php";
      $text = (string) file_get_contents ( $file );
      $type = include $file;

      preg_match ( '/\A<\?php\s*\n((?:[ \t]*\/\/[^\n]*\n)+)/', $text, $match );

      $lines    = array_map ( fn ( $line ) => preg_replace ( '/^\s*\/\/ ?/', '', $line ), explode ( "\n", rtrim ( $match [1] ?? '' ) ) );
      $about    = [];
      $examples = [];

      foreach ( $lines as $line )
        if ( preg_match ( '/^  (=\S.*?)(?:[ \t]{2,}(\S.*))?$/', $line, $one ) )
          $examples [] = [ 'formula' => $one [1], 'want' => $one [2] ?? '', 'shown' => sheetsExample ( $one [1] ) ];
        elseif ( trim ( $line ) !== '' )
          $about [] = trim ( $line );

      $about = implode ( ' ', $about );
      $about = preg_replace ( '/^' . preg_quote ( $name, '/' ) . ' - /', '', $about );

      $functions [] = [
        'name'     => strtoupper ( $name ),
        'kind'     => $name,
        'group'    => $type ['group'] ?? 'Other',
        'about'    => $about,
        'examples' => $examples
      ];

    }

    return $functions;

  }

  // A formula as a cell beside the sample sheet shows it - in G1, as a spreadsheet would show
  // it: through the format it takes from its cells or its function.

  function sheetsExample ( $formula ) {

    static $rows = NULL;

    $rows ??= padSheetRead ( sheetsSample () );

    $sheet = padSheetFromRows ( $rows );

    padSheetPut ( $sheet, 7, 1, padSheetTyped ( $formula ) );
    padSheetCompute ( $sheet );

    $cell = $sheet ['cells'] [1] [7];

    return padSheetShow ( $cell ['result'], $cell ['format'] );

  }

  // The functions by group, the groups in the order a spreadsheet's menu has them.

  function sheetsGroups () {

    $groups = [];

    foreach ( sheetsFunctions () as $function )
      $groups [ $function ['group'] ] [] = $function;

    $order  = [ 'Math', 'Statistics', 'Logic', 'Text', 'Lookup', 'Date and time', 'Finance' ];
    $sorted = [];

    foreach ( $order as $group )
      if ( isset ( $groups [$group] ) )
        $sorted [] = [ 'group' => $group, 'id' => strtolower ( str_replace ( ' ', '-', $group ) ), 'functions' => $groups [$group] ];

    foreach ( $groups as $group => $functions )
      if ( ! in_array ( $group, $order, TRUE ) )
        $sorted [] = [ 'group' => $group, 'id' => strtolower ( str_replace ( ' ', '-', $group ) ), 'functions' => $functions ];

    return $sorted;

  }

?>
