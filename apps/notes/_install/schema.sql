-- The notes database: built on the first request (padSqlSetup), on a server or in the
-- browser. The welcome notes say what the application is.

CREATE TABLE notes (
  id       INTEGER PRIMARY KEY AUTOINCREMENT,
  title    TEXT    NOT NULL,
  body     TEXT    NOT NULL DEFAULT '',
  color    TEXT    NOT NULL DEFAULT 'plain',
  pinned   INTEGER NOT NULL DEFAULT 0,
  created  TEXT    NOT NULL,
  updated  TEXT    NOT NULL
);

CREATE TABLE tags (
  note_id  INTEGER NOT NULL REFERENCES notes (id) ON DELETE CASCADE,
  tag      TEXT    NOT NULL,
  PRIMARY KEY (note_id, tag)
);

CREATE TABLE attachments (
  id       INTEGER PRIMARY KEY AUTOINCREMENT,
  note_id  INTEGER NOT NULL REFERENCES notes (id) ON DELETE CASCADE,
  name     TEXT    NOT NULL,
  file     TEXT    NOT NULL,
  type     TEXT    NOT NULL,
  size     INTEGER NOT NULL,
  created  TEXT    NOT NULL
);

CREATE INDEX tags_tag ON tags (tag);
CREATE INDEX attachments_note ON attachments (note_id);

INSERT INTO notes (title, body, color, pinned, created, updated) VALUES
('Welcome to Notes', 'Notes is a PAD application that keeps everything in **one SQLite database**.

- On a server it is the file `DATA/notes/notes.sqlite`.
- Packed with `pad pack notes` it runs in your browser, with no server at all: PHP compiled
  to WebAssembly answers every page, and the database is kept in the browser between visits.
- Served over https it installs as an application and works offline.

Write in Markdown, tag a note, attach a picture or a PDF, pin what matters. The **Backup** page
takes all of it with you - as JSON, or as the database file itself.', 'sun', 1, '2026-10-01 09:00:00', '2026-10-01 09:00:00'),
('Markdown, briefly', '## A heading starts with hashes

Text in **bold**, in *italics*, or as `code`.

- a list
- of things

1. numbered
2. as well

> A quote, for what someone else said.

[A link](https://example.org) and a rule:

---

That is all it takes.', 'sea', 0, '2026-10-02 10:30:00', '2026-10-02 10:30:00'),
('Weekend', '- Bake bread on Saturday morning
- Bike along the river - the long loop
- Call Ann about the trip in November
- Finish the book (the one with the lighthouse)', 'mint', 0, '2026-10-03 18:15:00', '2026-10-03 18:15:00'),
('Ideas for the offline shop', 'Orders written on the market, without network, and sent in the evening.

The database goes with the tablet; the **Backup** page hands it to the office as a file.', 'lilac', 0, '2026-10-04 08:45:00', '2026-10-04 08:45:00');

INSERT INTO tags (note_id, tag) VALUES
(1, 'pad'), (1, 'offline'), (2, 'markdown'), (2, 'pad'), (3, 'personal'), (4, 'ideas'), (4, 'offline');
