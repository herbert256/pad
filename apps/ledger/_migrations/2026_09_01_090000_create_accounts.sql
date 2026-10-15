-- The accounts money is kept in, and the categories it goes to or comes from.

CREATE TABLE accounts (
  id       INTEGER PRIMARY KEY AUTOINCREMENT,
  name     TEXT NOT NULL UNIQUE,
  kind     TEXT NOT NULL DEFAULT 'bank',
  opening  REAL NOT NULL DEFAULT 0
);

CREATE TABLE categories (
  id       INTEGER PRIMARY KEY AUTOINCREMENT,
  name     TEXT NOT NULL UNIQUE,
  kind     TEXT NOT NULL DEFAULT 'expense',
  color    TEXT NOT NULL DEFAULT '#64748b'
);
