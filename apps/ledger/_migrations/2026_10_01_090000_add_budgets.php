<?php

  // A later version: a monthly budget per category of spending. A database made by the
  // first version - on a server, or kept in a browser - gets the table on its next request,
  // with a budget for the categories it has, its transactions untouched.

  return [

    'up' => function () {

      db ( "CREATE TABLE budgets (
              category_id  INTEGER PRIMARY KEY REFERENCES categories (id) ON DELETE CASCADE,
              monthly      REAL NOT NULL )" );

      $defaults = [ 'Groceries' => 450, 'Dining out' => 160, 'Transport' => 120, 'Utilities' => 190,
                    'Fun' => 120, 'Health' => 60, 'Home' => 100, 'Rent' => 1250 ];

      foreach ( $defaults as $name => $monthly )
        db ( "INSERT INTO budgets (category_id, monthly) SELECT id, {1} FROM categories WHERE name = '{0}'", [ $name, $monthly ] );

    },

    'down' => "DROP TABLE budgets;"

  ];

?>
