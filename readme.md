# Slim PHP Boilerplate

![Slim PHP Boilerplate](/public/assets/img/slim-php-boilerplate.jpg)

---

## Author
Javier Aramayo \<javierf.aramayo@gmail.com>
Web developer and student of System's Analyst
My technologies stack: `PHP`, `Node.js`, `Python`, `HTML5`, `CSS`, `Javascript`, `C#`, `Visual Basic .NET`, `MySQL`, `MongoDB`

***

## What's that?

A little boilerplate created with Slim Framework to PHP with some additions to start a project very easy and fast. You have to download the folder's project, install dependencies, set your environment variables, create your database and you'll be ready to start coding.

## Requirements

- PHP 5.3 or more
- Composer
- PHP Server (like XAMPP, WAMPP or other)
- Mysql Database (This example is maded with MariaDB from XAMPP, but it can be modified to use other Datbase Engine).

## What's included?

- Project's folders structure to work with MVC.
- Slim configuration.
- Flexibility to create a Web App using PHP Sessions and cookies or an API REST using JSON WEB TOKENS.
- Dependencies:
    - __Slim Twig__ to render views.
    - __Dotenv__ to read environment variables (You have to set your own variables).
    - __Monolog__ to manage logging your Exceptions (Log files will be stored in a "Storage" folder created for that propose).
    - __Firebase PHP JWT__ to work with JSON WEB TOKENS.
    - __Slim CSRF__ to prevent CSRF attacks in your forms through a middleware.
    - __Faker__ to populate your database in development and make tests.
    - __PHP Mailer__ to send mails with HTML5 templates.
- An Authentication system maded with the folowing pages:
    - __Sign Up:__ To register users in your database.
    - __Sign In:__ To authenticate with email and password created in sign un process.
    - __Forgot Password:__ To reset password in database, this page requires the email registered and send a mail with a url to set a new password. Sended url is valid for the time you set in environment variable `EMAIL_TOKEN_EXPIRATION_TIME`.
- A script to generate a "Test database" in MySQL.
- Three custom classes created to easily manage your Database queries like a little "ORM", but so simple.
- For the views, the project is using MDB Boostrap and Jquery [https://mdbootstrap.com/docs/jquery/] but if you want you can modify the views to use your own HTML, CSS and JS.

# Basic Usage
1. Download or clone this project and put in your "htdocs" folder if you are using XAMPP, in case of using WAMPP the folder must be at "www", in other servers must be in the folder you use to run your projects.

2. Go to the .env.example file at the root folder and set your own data.

```
APP_NAME="My app" <-- Your app name

DDBB_NAME=my_database_name <-- Your database name

DDBB_HOST=localhost <-- Your host name

DDBB_USER=root <-- Your database user

DDBB_PASS=secret <-- Your database pass

EXPIRATION_TIME=540 <-- Time to expiration of PHP SESSION in minutes

PRIVATE_KEY=private_secret <-- Your key to encrypt Json Web Tokens

TIME_ZONE="America/Argentina/Cordoba" <-- Your timezone

APP_PUBLIC_URL=http://localhost/my-folder/public <-- Url to your public folder

EMAIL_HOST="smtp.gmail.com" <-- Your mail host

EMAIL_USER="my_email@gmail.com" <-- Your email user

EMAIL_PASS="secret" <-- Your mail password

EMAIL_PORT=587 <-- The port to send emails

EMAIL_TOKEN_EXPIRATION_TIME=1440 <-- Time to expiration of your JSON WEB TOKENS in minutes
```
3. Rename file ".env.example" to ".env".

4. Run the database script (`db.sql` at root folder) on your phpmyadmin or on any tool that you use for your database.

5. Install the dependencies of the project using
```
composer install
```
5. Go to your browser an put the url to your public folder.

***

## Verifications before running

- Sometimes email shields of Antivirus blocks the port to send mails and PhpMailer can't send. If this happens you must deactivate email shield from your antivirus or check for another application that is blocking email output.

- If you will use your Gmail to send mails you have to configure your account to allow this. Go to your Google account's settings > Security > Access for unsafe applications (or something like that) > Allow access.

- Check if the folder's structure in your downloaded project is ok to avoid errors in the execution. You can see that at the end of this page.

***
## Database classes

There are some classes created to easily manage database queries:

__Connection class:__ It's an abstract class to make the db connection through PDO and contains some functions to execute queries. In case of you want to do your own queries without using the other classes you must create your own class that extends from this.

__Model class:__ This class contains the functions you'll use to run your queries in a simple way, everything you have to do is create your own model that extends from "Model" and you'll inherit all of functions. At this project there is an example called `User` in model's folder.

In your own class you must declare this variables in `__construct()` function and run `parent::_construct()`:
- __table (`String`):__ This will contains the table in database to run queries when you call this model.

- __primaryKey (`String`):__ This propperty is optional, by default will be "id", but if you created your table using another name for your primary key you must configurate here.

- __fields (`String`):__  Here you indicates the fields of the tables that intervene in the query when you run a common `SELECT`.

- __joins (`String`):__ There will be all `JOINS` of the main table of this model.

- __create_fields (`Array`):__ You are setting the fields that will be filled when you run an `INSERT` query. Take careful to put here all fields that can not be null in database, otherwise you can not make insert correctly.

There is an example:

```
use App\Models\Model;

class User extends Model {

  public function __construct()
  {
    $this -> table = 'users';
    $this -> primaryKey = 'id';
    $this -> fields = "{$this->table}.*, s.status ";
    $this -> joins = "INNER JOIN status s ON s.id = {$this->table}.status_id ";
    $this -> create_fields = ['name', 'last_name', 'email', 'pass', 'status_id'];

    parent::__construct();
  }
}
```
## Available functions for queries

First you must create an instance of your model:

```
use App\Models\User;

$user = new User();
```
After that you'll be available to run any function you need:

---
## find()

```
$users = $user -> find();
```
If the query finds more than one user you'll receive an array of objects of the called class, in this case will be an array of instances of `User` class`, then you can iterate the array and use the propperties you need. For example:

```
foreach($users as $user){
  echo $user -> id;
}
```
If query finds only one user you'll receive an object of the called class, in this case will be an instance of `User`. Let's see that:

```
$oneUser = $user -> find();

echo $oneUser -> id;
```
Alternatively if you want to find users by `id` you can send an array of ids in find query __(the id must be numeric to use this)__.

```
$someUsers = $user -> find([1, 2, 3]);
```
If you send only one id you'll receive an object of the called class, instead if you send various ids you'll receive an array of objects.

___If the query doesn't find any row will return a `boolean` result `false`___

---

## first()

This function will set a `LIMIT 1` at the end of your query and will return only one row instantiating an object of the called class, if there's no results will return a `boolean` result `false`.

```
$oneUser = $user -> first();

echo $oneUser -> name;
```
---
## count()

Returns the amount of rows that match your query.
```
$howManyUsers = $user -> count();

echo $howManyUsers;
```
---
## props()

This function needs an `Array` of `Strings` which are the fields of the table you want to receive in the response.
```
// Example looking for multiple users

$usersByProps = $user -> props(['name', 'last_name']) -> find();

foreach($usersByProps as $user){
  echo $user -> name;
  echo $user -> last_name;
}

// Example looking for one user

$userByProps = $user -> props(['name', 'last_name']) -> first();

echo $userByProps -> name;
echo $userByProps -> last_name;
```
---
## where() and whereNot()

These functions needs 2 parameters which are `Strings`, the first is the name of the field in the table and the other is the value you are looking for in case of __where__, or the value you are not looking for in case of __whereNot__.
```
// Example looking for multiple users

$usersWhere = $user -> where('name', 'Jhon') -> find();

foreach($usersWhere as $user){
  echo $user -> name;
  echo $user -> last_name;
}

$usersWhereNot = $user -> whereNot('name', 'Jhon') -> find();

foreach($usersWhereNot as $user){
  echo $user -> name;
  echo $user -> last_name;
}

// Example looking for one user

$userWhere = $user -> where('name', 'Jhon') -> first();

echo $userWhere -> name;
echo $userWhere -> last_name;

$userWhereNot = $user -> whereNot('name', 'Jhon') -> first();

echo $userWhereNot -> name;
echo $userWhereNot -> last_name;

// Multiple conditions

$userWithMultipleWhere = $user -> where('name', 'Jhon')
                               -> where('last_name', 'Doe')
                               -> whereNot('status', 'Blocked')
                               -> first(); // or can be find(), or count()

echo $userWithMultipleWhere -> name;
echo $userWithMultipleWhere -> last_name;
```
---
## exists() and notExists()

Both functions returns a `boolean` value related to the amount of rows that match your query.
```
$userExists = $user -> where('name', 'Jhon') -> exists();

echo $userExists; // true if any match, false otherwise

$userNotExists = $user -> where('name', 'Jhon') -> notExists();

echo $userNotExists; // true if no rows, false otherwise
```
---
## like()

This function needs 2 parameters which are `Strings`, the first is the name of the field in the table and the other is the value you are looking for a match.
```
$usersLike = $user -> like('name', '%oh%') -> find();

$usersLike = $user -> like('name', 'oh%') -> first();

// Combining with other functions

$usersLike = $user -> like('name', '%oh')
                   -> where('status', 'Active')
                   -> where('country', 'USA')
                   -> whereNot('mail', 'jhon.doe@mail.example')
                   -> find();
```
---
## whereIn() and whereNotIn()

These functions needs 2 parameters, the first is a `String`, and it's the name of the field in the table, and the other is an `Array` of the values you are looking for in case of __whereIn__, or the values you are not looking for in case of __whereNotIn__.
```
$usersWhereIn = $user -> whereIn('name', ['Jhon', 'Elsa']) -> find();

$usersWhereNotIn = $user -> whereNotIn('status', ['Blocked', 'Deleted'])
                         -> find();
```
---
## or(), orNot() and orLike()

You must pass 2 parameters to these functions, both are `String`, the first is the name of the field in the table, and the other is the value you are looking for in case of __or__ and __orLike__, or the value you are not looking for in case of __orNot__.

Any of these three functions should be placed after a __where()__, __whereNot()__, __whereIn()__ or __whereNotIn()__, it should never be the first function called.
```
$userOr = $user -> where('name', 'Jhon')
                 -> or('last_name', 'Doe')
                 -> orNot('name', 'Elsa')
                 -> first();

$usersOrLike = $user -> like('name', 'Jho%')
                     -> orLike('last_name', '%oe')
                     -> find();
```
---
## orderBy()

This function receives two `String` as parameters, the first is the field of the database you will use to order your result and the second is the type of ordering, the last one can be `ASC` or `DESC`.

You should place that before the function that executes the query like __find()__, __first()__, or __foundRows()__.
```
$userOrderedBy = $user -> where('status', 'Active')
                       -> whereIn('country', ['Argentina', 'USA'])
                       -> orderBy('name', 'ASC')
                       -> first();
```
---
## limit()

This function receives two `Integer` as parameters, the first is the limit of rows you want, and the second is the "offset" which means the row's number you want to ignore returning results.

You should place that before the function that executes the query like __find()__, __first()__, or __foundRows()__, and after __orderBy()__ function in case you are using it
```
// This will return the first five rows finded.

$userWithLimit = $user -> where('status', 'Active')
                       -> whereIn('country', ['Argentina', 'USA'])
                       -> orderBy('name', 'ASC')
                       -> limit(5)
                       -> find();

// The first two rows will be ignored, and will return the next five.

$userWithOffset = $user -> where('status', 'Active')
                        -> whereIn('country', ['Argentina', 'USA'])
                        -> orderBy('name', 'ASC')
                        -> limit(5, 2)
                        -> find();
```
---
## foundRows()

This function can be used instead of __find()__.
In response you will receive an `Array` with two fields: `data` where you will get the results of your query (remember that they will be an instance of the called class), and `found_rows` where will be the number of rows that matches your query, this make sense where you combine it with __limit()__ function. Let's see an example:

```
// Suppose there are 20 rows that matches with the folowing query, if we use foundRows() combined with a limit() of 5

$userWithLimit = $user -> where('status', 'Active')
                       -> whereIn('country', ['Argentina', 'USA'])
                       -> orderBy('name', 'ASC')
                       -> limit(5)
                       -> foundRows()

// We will receive an array like this in the response

Array("data" => [ Here will be the first 5 ], "found_rows" => "20")

// Now we know how many rows there are in the database, and we can render in our view something like "Showing 5 users of 20".
So the next step is run a query with an offset of 5, to show the next 5 users, and that's how we can do a pagination.

$userWithOffset = $user -> where('status', 'Active')
                        -> whereIn('country', ['Argentina', 'USA'])
                        -> orderBy('name', 'ASC')
                        -> limit(5, 5)
                        -> foundRows();
```
---
## create() and createAndGet()

After you create a new instance of your model you can set the attributes of this to `INSERT` in the database. ___Remember to fill all those that can not be null___.

The difference between those functions is that __create()__ will return an `Array` that contains two fields:
- `response`: which can be __'ok'__ or __'error'__ depending if query executes successfully or not.
- `id` (only if "response" is "ok"): contains the id of the inserted field.
- `message` (only if "response" is "error"): contains the error message.

On the other hand, __createAndGet()__ will return an object of the called class with all data of the inserted row.

```
$user->name = 'Jhon';
$user->last_name = 'Doe';
$user->email = 'jhon.doe@mail.example';
$user->pass = 'secret';
$user->status_id = 1;
$newUser = $user -> create();

echo $newUser["response"]; // 'ok'
echo $newUser["id"]; // '1'

// The output will be Array("response" => "ok", "id" => "1");

$user->name = 'Jhon';
$user->last_name = 'Doe';
$user->email = 'jhon.doe@mail.example';
$user->pass = 'secret';
$user->status_id = 1;
$newUser = $user -> createAndGet();

// In this case "$newUser" will be an User instance and you can do something like:

echo $newUser->id;
echo $newUser->name;
echo $newUser->last_name;
echo $newUser->status;
```
---
## update() and updateAndGet()

Both functions receives two parameters.
The first is an `Array` which contains the fields you want to update and the values to set, must be something like:

```
Array(
  "field_to_update" => "value to update",
  "another_field" => "another value"
);
```
The second parameter must be the id of the row you want to update.

__Important!__
___The name of the primary key of your table by default will be "id", in case you are using another name as primary key do not forget to set the "primaryKey" attribute in model's constructor.___

The difference between those functions is that __update()__ will return an `Array` that can contains one or two fields:
- `response`: which can be __'ok'__ or __'error'__ depending if query executes successfully or not.
- `message` (only if "response" is "error"): contains the error message.

On the other hand, __updateAndGet()__ will return an object of the called class with all data of the updated row.

```
$dataToUpdate = [
  "name" => "Jack",
  "last_name" => "Carter"
];

$idToUpdate = 1;

$userUpdated = $user -> update($dataToUpdate, $idToUpdate);

echo $userUpdated["response"]; // 'ok' or 'error'
echo $userUpdated["message"]; // only if 'response' is 'error'

//------------------------------------------------------------//

$dataToUpdate = [
  "name" => "Jack",
  "last_name" => "Carter"
];

$idToUpdate = 1;

$userUpdated = $user -> updateAndGet($dataToUpdate, $idToUpdate);

// In this case "$userUpdated" will be an User instance and you can do something like:

echo $userUpdated->name;
echo $userUpdated->last_name;
```
---
## delete()

This function receives only one parameter which is the "id" to delete.

__Important!__
___The name of the primary key of your table by default will be "id", in case you are using another name as primary key do not forget to set the "primaryKey" attribute in model's constructor.___

The response will be an `Array` that can contains one or two fields:
- `response`: which can be __'ok'__ or __'error'__ depending if query executes successfully or not.
- `message` (only if "response" is "error"): contains the error message.

Before executes the `DELETE` query will verify if the "id" exists in your table.

```
$idToDelete = 1;

$userDeleted = $user -> delete($idToDelete);

echo $userDeleted["response"]; // 'ok' or 'error'
echo $userDeleted["message"]; // only if 'response' is 'error'
```
---
## DB Class
In case you want to write the complete query for yourself withouth using the functions of "Model" class or you will not create a model you will have these static functions available, it is not necessary to create an instance of any object.

## getRawQuery()
This function is used to run a query that obtains data from the database. It needs two parameters that are required, these are:
- `query (String)` where you should place your entired query.
- `params (Array)` where you should send the values to bind before run your query.

The third parameter is optional and is `fetch (String)` where you are telling to database the fetch mode to return results, it can be "fetch" to return only one result which be an array with the propperties you requested in your query, if you use "fetchAll" the response will be an `Array` of `Array`, where each `Array` will be a row in your database, and the last fetch mode is "fetchColumn" to return only one column as response, it will be a `String`, for example:
```
use App\Models\DB;

$query = "SELECT u.name, u.last_name, s.status, c.name as country
          FROM users u
          INNER JOIN status s ON s.id = u.status_id
          INNER JOIN countries c ON c.id = u.country_id
          WHERE u.name LIKE :name
          AND c.name = :country
          ORDER BY u.name ASC";

$params = [
  ':name' => 'Jh%',
  ':country' => 'USA'
];

$user = DB::getRawQuery($query, $params, 'fetch');

// The output will be something like:

[ 'name' => 'Jhon',
  'last_name' => 'Doe',
  'status' => 'Active',
  'country' => 'USA' ];

// Another example using "fetchAll"

$query = "SELECT u.name, u.last_name, s.status, c.name as country
          FROM users u
          INNER JOIN status s ON s.id = u.status_id
          INNER JOIN countries c ON c.id = u.country_id
          WHERE s.status = :status
          AND c.name = :country
          ORDER BY u.name ASC";

$params = [
  ':status' => 'Active',
  ':country' => 'USA'
];

$users = DB::getRawQuery($query, $params, 'fetchAll');

// The output will be something like:

[
  0 => [
    'name' => 'Jhon',
    'last_name' => 'Doe',
    'status' => 'Active',
    'country' => 'USA'
  ],
  1 => [
    'name' => 'Ervin',
    'last_name' => 'Howell',
    'status' => 'Active',
    'country' => 'USA'
  ],
  2 => [
    'name' => 'Leane',
    'last_name' => 'Graham',
    'status' => 'Active',
    'country' => 'USA'
  ]
];

// Now using fetchColumn

$query = "SELECT s.status
          FROM status s
          INNER JOIN users u ON s.id = u.status_id
          WHERE u.id = :id";

$params = [
  ':id' => '1'
];

$user = DB::getRawQuery($query, $params, 'fetchColumn');

echo $user; // the output will be something like: 'Active'
```
---
## setRawQuery()
This function can be used to run a query you need to returns only a message with 'ok' or 'error' and needs two parameters, the first is an `String` that contains the query, the second is an `Array` with the params to bind before run the query. Let's see that:

```
use App\Models\DB;

$query = "DELETE FROM users WHERE id = :id";

$params = [
  ':id' => 1
];

$deleted = DB::setRawQuery($query, $params);

echo $deleted['response]; // 'ok'

// if there is an error when the query is executed

echo $deleted['response']; // 'error';
echo $deleted['message']; // 'The error's message';
```
---
## insertRawQuery()
This function can be used to `INSERT` a row in your database and returns the "id" of the inserted row. It needs two parameters, the first is an `String` that contains the query, the second is an `Array` with the params to bind before run the query. Let's see that:

```
use App\Models\DB;

$query = "INSERT INTO users VALUES (null, :name, :last_name, :email, :pass, :status, null)";

$params = [
  ':name' => 'Juan',
  ':last_name' => 'Spinelli',
  ':email' => 'jspinelli2@gmail.com',
  ':pass' => 'asd123',
  ':status' => 1
];

$newUser = DB::insertRawQuery($query, $params);

echo $newUser['response]; // 'ok'
echo $newUser['id]; // '1'

// if there is an error when the query is executed

echo $newUser['response']; // 'error';
echo $newUser['message']; // 'The error's message';
```

## Folder's Structure of the project
```
.
├───app
│   ├───controllers
│   ├───middlewares
│   ├───models
│   ├───utils
│   └───views
│
├───bootstrap
│
├───config
│
├───public
│   ├───assets
│   │   ├───css
│   │   ├───img
│   │   └───js
│   └───uploads
│       ├───files
│       └───images
│
├───resources
│   ├───css
│   ├───emailTemplates
│   ├───js
│   └───views
│       ├───components
│       └───layouts
│
├───routes
│
├───storage
│   ├───cache
│   ├───logs
│   └───sessions
.
```

```
MIT License

Copyright (c) 2019 Javier Aramayo <javierf.aramayo@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
```