## Tangent Solutions - PHP Assessment
Chris Kempen<br />
chris@phpalchemist.com

<br />

## Introduction
Greetings assessors! Thank you so much for reviewing my PHP assessment.<br />
Herewith a few features and details about this submission:

- this assessment was created with <b>PHP7.4</b>, and <b>Laravel8</b>
- please make sure your PHP7 environment meets the minimum Laravel requirements, found [here](https://laravel.com/docs/8.x/deployment#server-requirements).
- you'll also need <b>composer</b> installed and available
- please also make sure you have <b>sqlite3 installed</b> (outside of composer)
- I've included the `.env` file with the repo, which is unusual but will make the initial setup a lot easier

## Installation & Commands
1. PHP dependency installation: `composer install`
2. Database creation (sqlite by default): `php artisan migrate:fresh`
3. Database creation with seeding ___(to help with testing)___: `php artisan migrate:fresh --seed`
4. Running the unit tests: `php artisan test`
4. Server start-up: `php artisan serve`
3. Swagger docs URL: `http://127.0.0.1:8000/api/documentation`

## System Features
I've created this list to showcase some of the areas of the API assessment I focused on (and their associated features), to help with talking points:

- There are no hard dependencies to the underlying data store implementation, as I used a <b>repository design pattern</b> here between the data layer and the control layer.
- The current repository implementation is using Eloquent for its data layer interactions, and has been tested with <b>both MySQL and sqlite</b>.
- I even implemented my own (very simple) authentication system to comply with the repository design pattern and eliminate that dependency.
- <b>Gateway design patterns</b> have been employed within the repository implementations, as well as in the API logger middleware.
- The API logger has been built with 2 log writers: a <b>database writer</b> (again using the repository design pattern), and a simple <b>file writer</b>.
- All of the above has been achieved using <b>contracts</b>, which are bound in a service provider called the `TangentServiceProvider`.
- I've left the `User` and `Category` endpoints open, and without authentication, purely to assist in ease of testing.
- <b>API keys</b> may be retrieved from the `/api/v1/sneaky` endpoint (which, as the name suggests, is a sneaky way to get an API key for a particular user), again purely to assist in testing. This endpoint would normally not exist.
- Validation is being performed through custom <b>request classes</b>, and similarly resources are being delivered through custom <b>resource classes</b>.
- There is some <b>cascading</b> that will be performed in the data structures on data manipulation, for example: when a post is deleted, any associated comments on the post will also be deleted automatically.
- <b>Uniqueness</b> is also being maintained within the data store, specifically around post titles, user email addresses and category labels.
- Unit tests are performed using a freshly-seeded database (on every test), which employs an <b>in-memory sqlite instance</b>.