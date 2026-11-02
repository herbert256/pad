# Calendars

Gregorian calendar views selected by `{calendar:kind}`. Each kind is a PHP file in
`types/`; `calendar.php` dispatches it. Shared calendar helpers live in
`pad/subs/calendars/_lib/calendar.php`.

| Kind | First parameter | Default query key | Pair rows |
|---|---|---|---|
| `month` | `2026-11` | `month` | Weeks containing `days` |
| `mini` | `2026-11` | `month` | Weeks containing `days` |
| `week` | `2026-11-04`, any day in the week | `week` | Seven days, Monday first |
| `day` | `2026-11-04` | `day` | One day |
| `agenda` | `2026-11` | `month` | Days with events or holidays, in date order |
| `quarter` | `2026-Q4` or `2026-11` | `quarter` | Three months containing `weeks` |
| `year` | `2026` | `year` | Twelve months containing `weeks` |
| `timegrid` | `2026-11-02` | None | Rendered view; no pair rows |
| `resources` | `2026-11-02` | None | Rendered view; no pair rows |

For the seven date views, without a parameter the view reads its query key, then defaults to today. Navigation
preserves the page and other query values. An explicit fixed period suppresses navigation;
a parameter equal to the requested period retains it. `query='schedule'` supplies an
independent query key. Invalid request values fall back to today; an invalid fixed period
is an error under PAD's strict check. Week and day parameters must be real Gregorian dates.
Years range from 0000 to 9999. The week view clips its days at either endpoint;
navigation never links outside that range.

The seven date views accept the original options:

- `data`: a PAD data store, PHP array, data file or inline data literal of event rows.
- `date`: the event date field; otherwise guessed, including `DateTimeInterface` values.
- `title`: the event text field; otherwise the first other text field.
- `link`: an optional event link field, restricted to safe web links.
- `sunday`: start weeks on Sunday instead of Monday.
- `holidays`: a supported country or region, such as `nl` or `de-by`.

`agenda` also accepts `empty` to include days without events or holidays. Its empty
single form shows a message; its empty pair executes `@else@`. Events keep their input
order within each day, and appear on the day given by the existing calendar date parser
in the application's timezone. These are date views: an event is placed on its start
date, with no duration expansion or hourly positioning.

```html
{calendar:month data='appointments', date='when', title='what', link='url'/}
{calendar:week '2026-11-02', data='appointments', sunday/}
{calendar:day '2026-11-04', data='appointments'/}
{calendar:agenda '2026-11', data='appointments', empty/}
{calendar:quarter '2026-Q4', holidays='nl'/}
{calendar:year '2026', holidays='nl'/}
{calendar:mini '2026-11'/}
```

Day rows contain `date`, `day`, `weekday`, `month`, `other`, `today`, `weekend`,
`count`, `events`, and `class`. `holiday` exists only with `holidays=`. Week rows
contain `week` (ISO week number) and `days`. Month rows contain `month` (`YYYY-MM`),
`year`, `number` (1–12), localized `name`, event `count` and `weeks`. Month counts
exclude the neighboring days used to fill the grid. An existing store named `events`
can shadow a day's field; use `{level:events}` to select the field explicitly.

```html
{calendar:agenda '2026-11', data='appointments'}
  <h3>{$date}</h3>
  {level:events}<p>{$what}</p>{/level:events}
@else@
  <p>No appointments this month.</p>
{/calendar:agenda}

{calendar:year '2026'}
  <h2>{$name}</h2>
  {array:weeks}{array:days}{$day} {/array:days}<br>{/array:weeks}
{/calendar:year}
```

Names follow the request locale, using the same proleptic Gregorian calendar as the
grid. The new navigation and empty-state labels can be translated with
`calendar.previousPeriod`, `calendar.nextPeriod`, and `calendar.empty` in the locale
catalog. Default styles use `:where()` so application CSS wins, carry the CSP nonce,
and need no JavaScript. Week views stack on narrow screens; year and quarter grids
adapt their column count to the available space.

The [calendars application](../../../apps/calendars/README.md) demonstrates every kind.

## Timed events and resource bookings

`timegrid` and `resources` use interval records instead of date-only events. Each timed
record has `start` and `end` ISO timestamps with explicit offsets, plus optional `title`
and safe `link`. With `allDay=true`, start/end are dates and the end is exclusive.
`resources` also needs a `resource` ID on each event and a `resources` map of IDs to labels.

```html
{format:auto 'bookings', format='json'}
[{"title":"Planning","resource":"room-a","start":"2026-11-02T09:00:00+01:00","end":"2026-11-02T10:30:00+01:00"}]
{/format:auto}
{calendar:timegrid '2026-11-02', data='bookings', timezone='Europe/Amsterdam', days=1/}
{calendar:resources '2026-11-02', data='bookings', resources=['room-a'=>'Room A','room-b'=>'Room B'], timezone='Europe/Amsterdam'/}
```

The first parameter is a day; when absent it defaults to today, without reading a query
key. `days=1` or `days=7` chooses a day or aligned week (`sunday` changes the week start).
`timezone` defaults to UTC; `label` names the rendered view. These self-closing views show
real timed intervals, all-day rows and overlap lanes, with no iterable calendar pair rows.
They do not accept date-field mapping, holidays, query navigation or `empty`.

See [timegrid](../../../docs/subsystems/calendars/timegrid.md) and
[resources](../../../docs/subsystems/calendars/resources.md) for DST behavior and limits.
The application provides complete examples at `?timegrid` and `?resources`.
