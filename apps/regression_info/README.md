# Regression: the five info modes

## Introduction

Regression test for `$padInfo` with all five modes - stats, trace, track, xml, xref - and
every option each of them honours. The probe page renders a loop, a pipe and a sequence;
the index fetches it and asserts that every mode recorded something for that very request:
the stats header on the response, the trace tree and the track files grown, the xml level
dump appended, and the probe on the cross-reference's record. The crawl compares the index,
so a mode that stops recording turns its line from yes to NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the probe and states one verdict per mode |
| `probe.php/pad` | A page with a loop, a pipe and a sequence to record |
| `_config/config.php` | The five modes by name, every option on, `_common` off |
