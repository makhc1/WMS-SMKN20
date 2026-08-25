# Performance Log

| Idea | Baseline → Result | Verdict | Why |
|---|---|---|---|
| Fix N+1 Queries on Dashboard | 14 queries / load → 2 queries / load | kept | `DashboardController` previously ran a loop generating N+1 queries. Grouped by `DATE` to perform a single query. |
| Lazy-Load Dashboard Charts | ~1.1MB JS bundle → ~280KB JS main bundle (charts loaded asynchronously) | kept | Chart.js and ApexCharts were statically imported, causing massive initial chunk size. Used Vue `defineAsyncComponent` to dynamically load charts. |
