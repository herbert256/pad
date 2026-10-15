-- The board: its columns, the cards in them, and what happened - the activity, whose newest
-- id is the board's version: server-sent events tell every open window when it moved on.

CREATE TABLE columns (
  id        INTEGER PRIMARY KEY,
  name      TEXT    NOT NULL,
  position  INTEGER NOT NULL,
  wip       INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE cards (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  column_id  INTEGER NOT NULL REFERENCES columns (id),
  position   REAL    NOT NULL,
  title      TEXT    NOT NULL,
  body       TEXT    NOT NULL DEFAULT '',
  label      TEXT    NOT NULL DEFAULT '',
  due        TEXT    NOT NULL DEFAULT '',
  created    TEXT    NOT NULL,
  updated    TEXT    NOT NULL
);

CREATE TABLE activity (
  id       INTEGER PRIMARY KEY AUTOINCREMENT,
  at       TEXT    NOT NULL,
  what     TEXT    NOT NULL,
  card_id  INTEGER
);

CREATE INDEX cards_column ON cards (column_id, position);

INSERT INTO columns (id, name, position, wip) VALUES
(1, 'Backlog', 1, 0), (2, 'Doing', 2, 3), (3, 'Review', 3, 2), (4, 'Done', 4, 0);

INSERT INTO cards (column_id, position, title, body, label, due, created, updated) VALUES
(1, 1, 'Pack the shop for the tablets', 'Run `pad pack shop` and put the directory on the intranet - the tablets install it from there.', 'feature', '2026-10-24', '2026-10-01 09:00:00', '2026-10-01 09:00:00'),
(1, 2, 'Prices in the offline catalogue', 'The catalogue has to show the prices of the day it was packed.', 'feature', '', '2026-10-01 09:05:00', '2026-10-01 09:05:00'),
(1, 3, 'Photos of the new collection', '', 'idea', '', '2026-10-01 09:10:00', '2026-10-01 09:10:00'),
(1, 4, 'Order form remembers the last customer', '', 'idea', '', '2026-10-02 10:00:00', '2026-10-02 10:00:00'),
(2, 1, 'Sync orders when the network is back', 'Export the orders as JSON from the backup page, import them in the office.', 'feature', '2026-10-18', '2026-10-02 11:00:00', '2026-10-06 14:00:00'),
(2, 2, 'Totals off by a cent', 'Round once, at the end - not per line.', 'bug', '2026-10-16', '2026-10-03 08:30:00', '2026-10-07 09:00:00'),
(3, 1, 'Dark mode for the order screen', '', 'chore', '', '2026-10-03 13:00:00', '2026-10-08 16:00:00'),
(4, 1, 'Install on the first tablet', 'Opened over https, installed from the address bar - works in airplane mode.', 'chore', '', '2026-10-01 15:00:00', '2026-10-05 12:00:00'),
(4, 2, 'Choose SQLite for the orders', 'One file, kept in the browser between visits; exported whole from the panel.', 'feature', '', '2026-10-01 15:30:00', '2026-10-04 10:00:00');

INSERT INTO activity (at, what, card_id) VALUES
('2026-10-04 10:00:00', 'Choose SQLite for the orders went to Done', 9),
('2026-10-05 12:00:00', 'Install on the first tablet went to Done', 8),
('2026-10-06 14:00:00', 'Sync orders when the network is back went to Doing', 5),
('2026-10-07 09:00:00', 'Totals off by a cent went to Doing', 6),
('2026-10-08 16:00:00', 'Dark mode for the order screen went to Review', 7);
