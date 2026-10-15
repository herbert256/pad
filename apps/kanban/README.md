# Kanban

## Introduction

A task board - cards dragged across its columns, every open window kept in step by server-sent events - on SQLite, on a server or in the browser when `pad pack kanban` runs it offline, where two windows of the installed application share one database.

## Overview

| Page | What it does |
|------|--------------|
| `index` | The board: the columns are `{fragment 'columns'}`, the activity `{fragment 'activity'}`; a column over its work-in-progress limit is marked; the New card dialog posts here |
| `card` | One card changed, moved to another column or deleted - or a new one |
| `move` | A move: JSON in from `kanban.js` - the card, its column, the card it goes before - with `X-CSRF-Token`; the board's version and the cards per column out (`$padExpose`) |
| `events` | `padSse`: every two seconds the board's version - its newest activity - and a `board` event when it is not the one the window had (`Last-Event-ID`) |

## Live, on a server and in the browser

`kanban.js` listens to `?events` with an `EventSource` and asks for the two fragments again
when the version went up. On a server the stream runs for a minute and the browser reconnects.
In the browser `padSse` answers one round per connection - one PHP answers every request of
the application, and a stream that held it would hold the board - and `EventSource` comes back
after the two seconds by itself, with the last id. Opened from the disk, two tabs each run PHP
and tell each other what they wrote; installed, one service worker runs PHP for both windows.

## The database

`_config/config.php` makes the application database SQLite - `DATA/kanban/kanban.sqlite`,
built from `_install/schema.sql` on the first request. A card's position is a number halfway
between its neighbours, so a move changes one row.

```bash
pad pack kanban --serve    # http://127.0.0.1:8090/ - open it in two windows and move a card
```
