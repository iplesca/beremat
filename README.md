# Beer-0-mat

The framework of choice used is Laravel 5.3.

## Bit of description
Displays a single page that fetches one random beer using the BreweryDB API. On page refresh NO new random beer is fetched (unless clicking the **random beer** button).

There is a main controller, *DashboardController* with 4 main action (homepage, randomBeer, sameBrewery and searchForm).

## Installation

1. Clone this repo
2. Run `composer update`
3. Create a local virtual host to point to the `/public` subfolder
4. Create a `.env` file with the following content and *edit* the APP_URL and DB_* with your local settings.
Feel free to change APP_DEBUG from _debug_ to _production_
```
APP_ENV=local
APP_KEY=base64:vWiJRE+2huubGILSbXgWLgUoZuOOT28BHeZViTrj7wY=
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_URL=http://distilled.localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=distilled
DB_USERNAME=root
DB_PASSWORD=1

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

PUSHER_APP_ID=
PUSHER_KEY=
PUSHER_SECRET=
```
5. Execute Laravel command `php artisan migrate` to create the tables;
6. Mind permissions :)

## Frontend
Plain and simple Bootstrap. 
A jQuery/AJAX approach would've presented a more eye-candy experience.

## Caching
The desired method was to create a local db copy of all the data. This is not fully implemented.
The minimal caching happens when requesting a random beer (will ask/store 10 records) in session. Similar behaviour for the currently displayed beer.

## Envisaged architecture
The controllers ask for data from *Dispatchers (beer, brewery, search etc.). 
The dispatchers would run as singletons with auto-configuration regarding the data source (either database only, API only or mixed "fail system").
The API methods called by the dispatchers go through another API layer (`BreweryDbApi`) that manage the communication (validation, exceptions etc.)

## Testing
No unit test available. Sorry.

## Final note
I enjoyed making this application, even though it's not complete (to the desired standard). 
The main challenge was to stop my mind going full blown settings/complicated architecture. I guess I managed to make a mix of it :)
