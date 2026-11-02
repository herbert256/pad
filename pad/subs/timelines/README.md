# Timelines

`timeline:*` draws events and spans from the same input rows as charts. The old PAD timeline tag is removed.

| Kind | Output |
| --- | --- |
| `horizontal` | Established SVG time axis with cards and span bars. |
| `vertical` | Established SVG chronological list, with dates beside cards. |
| `auto` | Chooses the established layout using `vertical=` at runtime. |
| `roadmap` | Responsive period columns, grouped lanes and milestone badges. |
| `swimlane` | Named lanes on a shared SVG time scale, bars and milestone diamonds. |

Pass rows through `data=` (page array, store, data file, JSON) or use the pair body as JSON, YAML, XML or CSV. `date=` names a moment; `from=` and `to=` name span endpoints. Missing explicit fields are inferred as before. `label=`, `text=`, `color=`, `format=` and accessible `title=` are shared. Dates support years, months, ISO dates and date objects; explicit zones are displayed in the application timezone. An empty result takes `@else@`.

`roadmap` groups by `period='month|quarter|year'` (default quarter). A span appears in its starting period and shows its full date range. `lane='team'` groups cards within each period. `swimlane` uses that same lane field, or the color field when omitted, and keeps overlapping events on separate rows. `milestone='release'` reads true, 1, yes or on; without it, point events are milestones. All user text is escaped.

`width=` controls the SVG width (720 horizontal, 600 vertical, 900 swimlane). Horizontal supports `height=`; more than 150 events fall back to the efficient vertical layout. Roadmap uses available container width. These views add no JavaScript or scheduling/dependency calculations; use `chart:gantt` for Gantt charts.

Runnable examples: `apps/tags/examples/timeline/`. Helpers live entirely in this subsystem; `padTimelineDate`, `padTimelineEvents`, `padTimeline` and `padTimelineColumn` retain their signatures after including `_lib/timeline.php`.
