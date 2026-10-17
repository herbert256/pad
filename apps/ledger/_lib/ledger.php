<?php

  // The books: months, totals, spending per category, budgets, the rows of a month - what
  // the pages of Ledger share.

  // The database as this version of the application wants it: the pending migrations run,
  // and a database made from nothing gets its half year of examples. Asked once a request,
  // before any page reads it - the first request builds it, a new version's first request
  // brings it up to date.

  function ledgerReady () {

    $pending = array_filter ( padMigrateStatus () ?: [], fn ( $one ) => ! $one ['ran'] );

    if ( ! $pending )
      return;

    // Made from nothing: no migration had run. The examples go in only when this request
    // ran the migrations - another one that came at the same moment waited for the lock and
    // found nothing left to run.

    $fresh = ! array_filter ( padMigrateStatus () ?: [], fn ( $one ) => $one ['ran'] );
    $ran   = padMigrate ();

    if ( $ran === FALSE )
      return padError ( 'Ledger could not bring its database up to date: ' . padMigrateError () );

    if ( $fresh and $ran )
      padSeed ();

  }

  // The months there are transactions in, the newest first - and the one a page shows: the
  // asked one when there is such a month, else the newest.

  function ledgerMonths () {

    return array_column ( db ( "ARRAY DISTINCT substr(day, 1, 7) AS month FROM transactions ORDER BY month DESC" ) ?: [], 'month' );

  }

  function ledgerMonth ( $asked ) {

    $months = ledgerMonths ();

    if ( preg_match ( '/^\d{4}-\d\d$/', (string) $asked ) and in_array ( $asked, $months, TRUE ) )
      return $asked;

    return $months [0] ?? date ( 'Y-m' );

  }

  function ledgerMonthName ( $month ) {

    return date ( 'F Y', strtotime ( "$month-01" ) );

  }

  // Income, spending, what is left and the share kept, for one month - money moved between
  // the accounts is neither.

  function ledgerTotals ( $month ) {

    $row = db ( "RECORD coalesce(sum(CASE WHEN amount > 0 THEN amount END), 0) AS income,
                        coalesce(-sum(CASE WHEN amount < 0 THEN amount END), 0) AS spent,
                        count(*) AS entries
                 FROM transactions WHERE day LIKE '{0}-%'
                   AND category_id NOT IN (SELECT id FROM categories WHERE kind = 'transfer')", [ $month ] );

    $income = round ( (float) $row ['income'], 2 );
    $spent  = round ( (float) $row ['spent'], 2 );

    return [
      'income'  => $income,
      'spent'   => $spent,
      'left'    => round ( $income - $spent, 2 ),
      'kept'    => $income > 0 ? (int) round ( 100 * ( $income - $spent ) / $income ) : 0,
      'entries' => (int) $row ['entries']
    ];

  }

  // The six months up to this one: income and spending each, for the bar chart.

  function ledgerHistory ( $month, $count = 6 ) {

    $rows = [];

    for ( $i = $count - 1; $i >= 0; $i-- ) {
      $one    = date ( 'Y-m', strtotime ( "$month-01 -$i month" ) );
      $totals = ledgerTotals ( $one );
      $rows [] = [ 'month' => date ( 'M', strtotime ( "$one-01" ) ), 'income' => $totals ['income'], 'spending' => $totals ['spent'] ];
    }

    return $rows;

  }

  // What a month's money went to, by category, the largest first.

  function ledgerSpending ( $month ) {

    return array_values ( db ( "ARRAY c.name AS category, round(-sum(t.amount), 2) AS spent, c.color
                                FROM transactions t JOIN categories c ON c.id = t.category_id
                                WHERE t.day LIKE '{0}-%' AND t.amount < 0 AND c.kind = 'expense'
                                GROUP BY c.id ORDER BY spent DESC", [ $month ] ) ?: [] );

  }

  // Every category with a budget: what it may be, what went to it this month, the share.

  function ledgerBudgets ( $month ) {

    $rows = array_values ( db ( "ARRAY c.id, c.name, c.color, b.monthly,
                                       coalesce((SELECT round(-sum(amount), 2) FROM transactions t
                                                 WHERE t.category_id = c.id AND t.day LIKE '{0}-%' AND t.amount < 0), 0) AS spent
                                FROM budgets b JOIN categories c ON c.id = b.category_id
                                ORDER BY c.name", [ $month ] ) ?: [] );

    foreach ( $rows as &$row ) {
      $row ['share'] = $row ['monthly'] > 0 ? (int) round ( 100 * $row ['spent'] / $row ['monthly'] ) : 0;
      $row ['state'] = $row ['share'] > 100 ? 'over' : ( $row ['share'] >= 85 ? 'close' : 'fine' );
      $row ['left']  = round ( $row ['monthly'] - $row ['spent'], 2 );
      $row ['tone']  = [ 'over' => 'danger', 'close' => 'warning', 'fine' => 'success' ] [ $row ['state'] ];
    }

    return $rows;

  }

  // The transactions of a month - all, of a category, of an account, or with a word in the
  // note - newest first, with their category and account by name.

  function ledgerRows ( $month, $category = 0, $account = 0, $search = '' ) {

    $where = [ "t.day LIKE '{0}-%'" ];

    if ( $category ) $where [] = 't.category_id = {1}';
    if ( $account  ) $where [] = 't.account_id = {2}';
    if ( $search !== '' ) $where [] = "t.note LIKE '%{3}%'";

    return array_values ( db ( "ARRAY t.id, t.day, c.name AS category, a.name AS account, t.note, t.amount, c.kind
                                FROM transactions t
                                JOIN categories c ON c.id = t.category_id
                                JOIN accounts a ON a.id = t.account_id
                                WHERE " . implode ( ' AND ', $where ) . "
                                ORDER BY t.day DESC, t.id DESC", [ $month, (int) $category, (int) $account, $search ] ) ?: [] );

  }

  // The categories a transaction can be booked on - income and spending; transfers between
  // the accounts are what the month's close books.

  function ledgerCategories () {

    return array_values ( db ( "ARRAY id, name, kind, color FROM categories WHERE kind != 'transfer' ORDER BY kind DESC, name" ) ?: [] );

  }

  function ledgerAccounts () {

    return array_values ( db ( "ARRAY a.id, a.name, a.kind,
                                       round(a.opening + coalesce((SELECT sum(amount) FROM transactions t WHERE t.account_id = a.id), 0), 2) AS balance
                                FROM accounts a ORDER BY a.id" ) ?: [] );

  }

  // One transaction stored: the amount signed by its category - spending goes out, income
  // comes in, whichever sign was typed. Answers its id, or FALSE when the category or the
  // account is not there.

  function ledgerBook ( $day, $categoryId, $accountId, $amount, $note ) {

    $kind = db ( "FIELD kind FROM categories WHERE id={0}", [ (int) $categoryId ] );

    if ( ! in_array ( $kind, [ 'income', 'expense' ], TRUE ) or ! db ( "CHECK accounts WHERE id={0}", [ (int) $accountId ] ) )
      return FALSE;

    $amount = abs ( round ( (float) $amount, 2 ) ) * ( $kind == 'income' ? 1 : -1 );

    return db ( "INSERT INTO transactions (day, account_id, category_id, amount, note) VALUES ('{0}', {1}, {2}, {3}, '{4}')",
                [ $day, (int) $accountId, (int) $categoryId, $amount, mb_substr ( (string) $note, 0, 120 ) ] );

  }

  // The rows of an imported CSV file: the header's names in lower case, then each line as
  // those names, with its line number - an empty line is no row. fgetcsv answers [ NULL ]
  // for one, and the trim that was to skip it took NULL: the import ended in an error after
  // the rows above it were booked.

  function ledgerCsv ( $handle ) {

    $header = array_map ( fn ( $h ) => strtolower ( trim ( (string) $h, " \t\n\r\0\x0B\xEF\xBB\xBF" ) ), fgetcsv ( $handle, escape: '' ) ?: [] );
    $rows   = [];
    $line   = 1;

    while ( ( $cells = fgetcsv ( $handle, escape: '' ) ) !== FALSE ) {

      $line++;

      if ( count ( $cells ) == 1 and trim ( (string) $cells [0] ) === '' )
        continue;

      $rows [$line] = array_combine ( $header, array_pad ( array_slice ( $cells, 0, count ( $header ) ), count ( $header ), '' ) );

    }

    return [ $header, $rows ];

  }

  // The day of an imported row as Y-m-d, or NULL when it cannot be read surely. strtotime
  // read 02/09/2026 the American way, as 9 February, and skipped 13/09/2026: a European
  // bank's export - the form of the euro amounts this ledger keeps - went into the wrong
  // months without a word. A day of digits is read year first (2026-09-02) or day first
  // (02/09/2026, 2.9.2026, 02-09-2026), a time behind it left out; one with the name of a
  // month as strtotime reads it.

  function ledgerImportDay ( $text ) {

    $text = trim ( (string) $text );

    if ( preg_match ( '/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})(?:[ T][0-9:.]+)?$/D', $text, $m ) )
      [ , $year, $month, $day ] = $m;
    elseif ( preg_match ( '/^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})(?:[ T][0-9:.]+)?$/D', $text, $m ) )
      [ , $day, $month, $year ] = $m;
    elseif ( preg_match ( '/[a-z]/i', $text ) and ( $time = strtotime ( $text ) ) )
      return date ( 'Y-m-d', $time );
    else
      return NULL;

    return checkdate ( (int) $month, (int) $day, (int) $year ) ? sprintf ( '%04d-%02d-%02d', $year, $month, $day ) : NULL;

  }

  function ledgerMoney ( $amount ) {

    return ( $amount < 0 ? '−' : '' ) . '€ ' . number_format ( abs ( (float) $amount ), 2, '.', ',' );

  }

?>
