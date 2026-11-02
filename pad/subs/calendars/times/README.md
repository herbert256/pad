# Time displays

`time:*` owns the former countdown and timeago tag behaviour and adds clocks and stopwatches. The original PAD tags and their tag-library files are removed.

- `time:countdown '2026-12-31'`: time remaining, with `units=1..4`, `past=`, `now=`, `format=` for its full title, and optional `live` browser updates. Existing markup and script behaviour are retained.
- `time:relative $moment`: human relative time with exact `datetime` and full title. Supports `now=` and title `format=`; an empty moment produces nothing.
- `time:clock timezone='Europe/Amsterdam'`: a world clock, live by default. `live=0` is static; `now=` supplies a deterministic server moment; `label=` names it. Timezones are IANA identifiers or UTC. Formats: `H:i:s`, `H:i`, `Y-m-d H:i:s`, `Y-m-d H:i`, `h:i:s A`, `h:i A`. The live clock uses the browser's current time and timezone database, including daylight-saving transitions; it retains server output if the browser lacks that zone. Static clocks retain the server's `now=`.
- `time:stopwatch 65, label='Practice'`: initial elapsed seconds, default zero, with Start/Pause/Reset controls. `autostart` starts when initialized. Pausing retains elapsed time; reset stops and returns to zero. Each instance is independent. Elapsed time uses the browser's monotonic clock; it is not persisted across navigation or browser restarts.

All date views use `padDateParse`/`padNow`, including timestamps and DateTime objects. Shared helpers are under `_lib/`; `padCountdown` and `padTimeago` retain their signatures. Browser controllers are emitted once per output scope with the request nonce when CSP asks for one. There are no inline event handlers. Timers have quiet live regions; status changes are announced without announcing every tick. Without JavaScript clocks retain server text and stopwatch controls remain disabled.

Runnable examples are under `apps/tags/examples/time/`. Run `node pad/subs/calendars/times/test-browser.mjs` for clock and stopwatch controller checks.
