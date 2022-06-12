# Readme

This project integrates two datasets for the information integration lecture.

## Setup

This project uses Laravel. To use it:

- Clone this repository
- Run it like a regular Laravel Project
  - Recommendation: Use Laravel Sail, which is a docker environment
    - You can find all Information for this on https://laravel.com/docs/9.x/sail
    - Probably you have to have a local PHP environment installed
    - You should be able to use the regular `docker-compose up` to get everything running
- You have to run the database migrations: `sail artisan migrate`

## Importing data

We're using our own dump of the crawled articles (`articles.json`) as well as the the dump of the Handelsregisterbekanntmachungen (`corporate-events-dump`) provided by Leon Schmidt (see [Slack](https://bakdata.slack.com/archives/C03D68H4467/p1654793754328919?thread_ts=1654689762.692609&cid=C03D68H4467)). Place both within `storage/app/public`.

You can then import both datasets:
- `sail artisan import:articles`
- `sail artisan import:hrb`

Afterwards, you should flush all articles to the Meiliesearch included in the docker. (It is deactivated for the import as of performance reasons)

- `sail artisan scout:import "App\Models\Article"`

## Do the actual processing

(The part of exercise 3)

We've created two scripts for this exercise.

### Extracting Company Information

To extract the company information from the generic HRB-information field, run `sail artisan extract:company`. This goes through every entry, extracts the name of the company as well as the ZIP code and creates (or gets, if already existing) an entry in the companies table, which is then linked to the HRB entry.

As this process takes a lot of time (every entry generates at least two database queries which can't be parallelized for uniqueness constraints), we've limited the batch of HRBs to 100.000 per run of the script, but you can run it multiple times, it only uses HRBs which don't have a company linked yet.

### Search connections between company and articles

To search for each company for matching articles, run `sail artisan search:connection`. This then searches for every company within the search machine and linkes the matches in the pivot table (`article_company`).

This process is again limited to 10.000 entries in the company table, but can be run multiple times.
