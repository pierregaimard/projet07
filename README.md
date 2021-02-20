# projet07
BileMo: PHP/Symfony API Study project

## Code quality check
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e87b955e91be4ed8b21eb5bc4e8925b3)](https://www.codacy.com/gh/pierregaimard/projet07/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=pierregaimard/projet07&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/db27499db31597a587e4/maintainability)](https://codeclimate.com/github/pierregaimard/projet07/maintainability)

## Project installation

### Clone the repository
On your server, open a terminal, go to your web directory then launch
the following command:  
`git clone https://github.com/pierregaimard/projet07.git`

### Install project dependencies
Then jump into the project directory `cd projet07`
and install the project dependencies by running the command:  
`composer update`

### Setup .env variables
In the `.env` file, set up the different information:

#### Database URL
Add your database information to `DATABASE_URL` variable

#### JWT Secret
Change the JWT secret: `JWT_PASSPHRASE`

### Initialize the database
Now run the following commands to set up the database:
-   `php bin/console d:d:c` Database creation
-   `php bin/console d:m:m` Database schema migration
-   `php bin/console d:f:l` Load fixtures

### Generate the SSL Keys
Now you must generate the SSL Keys for JWT authentication by running
the command:
`php bin/console lexik:jwt:generate-keypair`

### Functional Tests
Then you need to run the tests to be sure that the API is functional:

The first step is to configure the database url for tests by setting
the `DATABASE_URL` variable in the `.env.test` file.

Then launch the tests by running the following command:  
`php bin/phpunit`

If the tests are all green you're done!.