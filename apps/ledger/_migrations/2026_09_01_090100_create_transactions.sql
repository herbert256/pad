-- What happened: a day, an account, a category, an amount - money in positive, money out
-- negative - and a note.

CREATE TABLE transactions (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  day          TEXT    NOT NULL,
  account_id   INTEGER NOT NULL REFERENCES accounts (id),
  category_id  INTEGER NOT NULL REFERENCES categories (id),
  amount       REAL    NOT NULL,
  note         TEXT    NOT NULL DEFAULT ''
);

CREATE INDEX transactions_day ON transactions (day);
