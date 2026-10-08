-- The customers; a quote inside a literal and a semicolon inside a comment; stay put.

create table customers (
  id    integer primary key autoincrement,
  name  varchar(80) not null,
  city  varchar(40) not null default 'Nowhere; really',
  email varchar(120)
);

create index customers_city on customers (city);
