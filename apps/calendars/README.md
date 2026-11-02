# Calendars Application

## Introduction

Nine calendar views from `pad/subs/calendars/types/`: month, mini, week, day,
agenda, quarter, year, timegrid and resources. The home page is a browsable month; each view has a rendered
example and its complete template and event data, with links to the live and WASM editors.

Run at `/pad/calendars/`. The menu and view catalog follow the files in the subsystem.
`calendar:kind` selects a view; `calendar:month` shows the month grid.
See [the subsystem](../../pad/subs/calendars/README.md) for parameters and pair row shapes.

The [timegrid example](_examples/timegrid.pad) uses explicit-offset intervals, overlapping
appointments, an all-day event and an interval crossing midnight. The
[resources example](_examples/resources.pad) maps bookings to two rooms and shows their
overlaps separately. Open `?timegrid` or `?resources` in the application. Both examples
include paired `{pad:data}` blocks, so their editor links run without external data.

Timed views render with a self-closing tag; calendar pair rows, date-field guessing,
holidays and query navigation belong to the seven date views. See the
[timegrid contract](../../docs/subsystems/calendars/timegrid.md) and
[resource contract](../../docs/subsystems/calendars/resources.md) for interval fields,
timezones and limits.
