# calendars: timegrid

`{calendar:timegrid '2026-11-02', data='events', timezone='Europe/Amsterdam', days=7/}`
adds an interval model alongside the existing date-only calendar views. Timed records
require `start` and `end` ISO timestamps with explicit Z or ±HH:MM offsets, e.g.
2026-11-02T09:00:00+01:00. Title and safe link are optional. `allDay=true` instead
requires YYYY-MM-DD start/end; end is exclusive. Zero or reversed intervals fail.
Timezone names are explicit; ambiguous/repeated local times are resolved by the
supplied event offsets, never guessed. A day spans its real local midnight bounds,
so spring/fall transitions occupy 23/25 hours and repeated hours display offsets.
`days` is 1 or 7; a week aligns Monday, or Sunday with `sunday`. Crossing-midnight
events split visually into each touched day. Overlap clusters receive independent
lanes. DOM order is chronological, every event has its complete dated interval as an
accessible name, and print falls back to ordinary lists. Limits: 2000 input events.
No recurrence expansion, dragging, booking validation or persistence is implied.
