<?php

  // What each category of spending may cost a month. A budget of 0 takes the category's
  // budget away; the overview shows how far each month got.

  if ( padPosted ( 'budgets' ) ) {

    foreach ( (array) padRequest ( 'budget', [] ) as $id => $amount ) {
      $amount = (float) str_replace ( ',', '.', (string) $amount );
      if ( $amount > 0 )
        db ( "INSERT OR REPLACE INTO budgets (category_id, monthly) SELECT id, {1} FROM categories WHERE id={0} AND kind = 'expense'", [ (int) $id, round ( $amount, 2 ) ] );
      else
        db ( "DELETE FROM budgets WHERE category_id={0}", [ (int) $id ] );
    }

    padFlash ( 'The budgets are saved.' );
    padRedirect ( 'index', [ 'month' => $ledgerMonth ] );

  }

  $plan = array_values ( db ( "ARRAY c.id, c.name, c.color, coalesce(b.monthly, 0) AS monthly
                               FROM categories c LEFT JOIN budgets b ON b.category_id = c.id
                               WHERE c.kind = 'expense' ORDER BY c.name" ) ?: [] );

  $planned    = array_sum ( array_column ( $plan, 'monthly' ) );
  $ledgerPage = 'Budgets';

?>
