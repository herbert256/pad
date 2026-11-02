# calendars: resources

`{calendar:resources '2026-11-02', resources=$rooms, data='bookings', timezone='Europe/Amsterdam'/}`
uses the timegrid interval model, adding resource columns. `resources` maps stable
IDs to labels (1–20); each event's `resource` must match exactly. Unknown resources
fail instead of hiding bookings. `days=1` is default; seven-day views also work.
Each resource/day has separate overlap lanes and all-day rows, with chronological
keyboard reading and printable lists. The application owns availability rules,
permissions, conflicts, recurrence expansion and booking persistence; overlap is
shown faithfully rather than being treated as a reservation approval.
