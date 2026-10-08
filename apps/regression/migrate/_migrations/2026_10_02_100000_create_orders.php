<?php

  // A PHP migration: the up is a function that works with db (), the down SQL text.

  return [

    'up' => function () {

      db ( "create table orders (
              id          integer primary key autoincrement,
              customer_id integer not null references customers (id),
              total       decimal(8,2) not null,
              placed      varchar(10) not null )" );

      db ( "create table order_log ( order_id integer, note varchar(40) )" );

    },

    'down' => "drop table order_log; drop table orders;",

  ];

?>
