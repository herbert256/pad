# au — Australia

Supported years: **2022–2027**. `padHolidays('au-nsw', 2026)` or `{holidays:auto 'au-act', year=2026/}` selects New South Wales or the Australian Capital Territory. `au` alone is the common national baseline, deliberately excluding region-dependent Easter weekend days, birthdays and labour days. Other region codes are rejected, not silently approximated. Local show/racing holidays, restricted trading rules, bank-only holidays and part-day holidays are outside this scope.

Australia Day is moved to Monday when 26 January falls on a weekend, with no extra public row for the weekend date. New Year, Christmas and Boxing Day retain the actual date and gain the next unoccupied weekday when on a weekend. The 2022 national mourning day is included. Anzac Day has the additional Monday in NSW/ACT in 2026 and 2027, per explicit declarations; the baseline does not invent that additional day nationally. ACT also includes Canberra Day and Reconciliation Day. A new year must be checked before extending coverage.

Sources checked **2 November 2026**:

- [NSW official 2026–2027 calendar and substitution notes](https://www.nsw.gov.au/about-nsw/public-holidays).
- [NSW Anzac additional-day announcement, 15 February 2026](https://www.nsw.gov.au/ministerial-releases/minns-labor-government-announces-extra-public-holiday-year).
- [ACT official calendar, including 2026–2027](https://www.act.gov.au/living-in-the-act/public-holidays-school-terms-and-daylight-saving).
- [ACT prior years, including the 2022 mourning-day declaration](https://www.act.gov.au/living-in-the-act/public-holidays-school-terms-and-daylight-saving/previous-years).
- [Fair Work Anzac guidance, 15 April 2026](https://www.fairwork.gov.au/newsroom/news/anzac-day-public-holidays).

The focused test checks full public-date sets against the ACT calendars for 2022, leap-year 2024, 2026 and 2027, NSW regional differences, 2025's moved Australia Day, unsupported region/year rejection and substitute flags.
