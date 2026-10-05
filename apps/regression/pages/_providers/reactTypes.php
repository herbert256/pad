<?php

  // The provider the misc/react_types test runs: a value keeps its type - a code "007" and a
  // year "2024" stay text, a number from PHP or an integer column from the database is a
  // number. {reactData} made every numeric-looking string a number.

  return [
    'php' => [ 'code' => '007', 'year' => '2024', 'n' => 7 ],
    'db'  => db ( "RECORD 7 AS n, '007' AS code" )
  ];

?>
