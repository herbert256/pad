# Focused subsystem checks

`bash tests/subsystems/run.sh` runs the independent PHP and JavaScript fixtures;
`./ci.sh` includes the same checks. The browser and external barcode oracle checks
remain explicit commands because they require Chrome or optional external tools.

Run PHP fixtures with `php tests/subsystems/<name>.php` and model fixtures with
`node tests/subsystems/<name>.cjs`. These test behavior independently of rendering.

`php tests/subsystems/layout.php` enforces the flat subsystem layout: every
registered family lives directly under `pad/subs/`, with no nested registries.

`node tests/subsystems/ui-browser.cjs` renders a combined PAD fixture with this
checkout's engine, then starts an isolated headless Chrome profile. Override the
macOS Chrome default with `PAD_CHROME=/path/to/chrome`. It checks normal and reduced
motion, interactions, keyboard focus, reset, fragment initialization, multiple
instances, print geometry and calendar overlap placement. It additionally loads the
same server markup with enhancement scripts removed to inspect native fallback
controls. Temporary fixture and log paths are printed at completion.

Printable verification also exercised PAD PDF and Chrome PDF: planner is one A4
page, flashcards two A4 faces, booklet two landscape A4 faces. Pairing and imposition
are independently tested with identifiable content and blank padding. Physical
printer registration, browser/OS assistive technology, and arbitrary long-content
pagination need application-specific checks; fixed printable cells intentionally
clip overflow. Calendar tests cover explicit-offset instants, DST day lengths,
repeated times, midnight clipping and overlap lane allocation.
