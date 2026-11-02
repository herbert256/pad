# recurrences: dates

Explicit occurrence set, with required mode=date or instant. Date mode accepts only ISO dates (including bounds/exclusions); instant mode requires whole-second datetimes or DateTimeInterface values. Values are normalized to the IANA timezone (UTC default), sorted and deduplicated by date or timestamp. Exclusions take precedence after deduplication; instant mode also permits local whole-day exclusions. Bounds [from,to). ≤10000 input dates and exclusions, output limit defaults 1000 (≤10000). No manufactured recurrence pattern or count/interval options.

Runnable example: `apps/tags/examples/recurrence/dates.pad`. Independent fixture: `php tests/subsystems/expansion-recurrences-dates.php`.
