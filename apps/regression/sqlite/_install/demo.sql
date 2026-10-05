-- The demo database's staff table, in SQLite, and a table for the writes.

CREATE TABLE staff (
  name   varchar(32),
  phone  varchar(32),
  salary decimal(8,2),
  bonus  decimal(8,2)
);

INSERT INTO staff VALUES
  ('bob',   '555-3425', 1000, 400),
  ('jim',   '555-4364', 2000, 300),
  ('joe',   '555-3422', 3000, 200),
  ('jerry', '555-4973', 4000, 100);

CREATE TABLE notes (
  id   integer PRIMARY KEY AUTOINCREMENT,
  text varchar(64)
);
