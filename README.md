# Web MVC Starter

A PHP 8.2 web application created side-by-side with [Lambert Mata](https://github.com/LambertMata) in order to help interns become familiar with the MVC design pattern.

---

- [Web MVC Starter](#web-mvc-starter)
    - [Requirements](#requirements)
      - [Linux environment](#linux-environment)
      - [Docker](#docker)
    - [Getting started](#getting-started)
      - [1. Initial MySQL configuration](#1-initial-mysql-configuration)
      - [2. Start the application with Docker](#2-start-the-application-with-docker)
    - [Application components](#application-components)
      - [2. Configure NGINX web server](#2-configure-nginx-web-server)
      - [4. Start the application with Docker](#4-start-the-application-with-docker)
    - [Dockerization](#dockerization)
    - [Web Server](#web-server)
  - [Project structure](#project-structure)
    - [Entrypoint](#entrypoint)
    - [Routes](#routes)
      - [Wild card](#wild-card)
    - [Request](#request)
    - [Parameter](#parameter)
    - [Controller](#controller)
    - [Response](#response)
    - [View](#view)
    - [ORM](#orm)
    - [Model](#model)
      - [Methods](#methods)
    - [Static Methods](#static-methods)
    - [Join Methods](#join-methods)
    - [Multiple WHERE Clauses](#multiple-where-clauses)
      - [Relationship](#relationship)
      - [Configuration](#configuration)
    - [Database Schema](#database-schema)


### Application Requirements

#### Linux environment
Our recommended approach, to work with the project, is to use the Windows Subsystem for Linux [WSL](https://learn.microsoft.com/en-us/windows/wsl/install).

#### Docker
You can install Docker using [Docker desktop](https://www.docker.com/products/docker-desktop/) which integrates with WSL automatically without any additional set up.

To enable WSL integration flag `Enable integration with my default WSL distro` in `Settings > Resources > WSL Integration`.


### Getting started
#### 1. Initial MySQL configuration
The first time the mysql container is started it will create a database and credentials that can be changed in `./docker/mysql/.env`. The default configuration is:

```shell
MYSQL_ROOT_PASSWORD=secret
MYSQL_USER=dev
MYSQL_PASSWORD=secret
MYSQL_DATABASE=app
```

#### 2. Start the application with Docker
The application uses Docker compose to create the infrastructure for development environment. The configuration is defined in ```docker-compose.yml``` and contains the following services:

* NGINX
* PHP-FPM
* MySQL

On each "development session" MySQL database data is persisted using a volume.

**Runing the application in detached mode**:
```shell
docker compose up -d
```

**Checking if the application is running correctly using**:
```shell
docker ps
```
you should see three containers running the services..

**Stopping the application**:
```shell
docker compose down
```

### Application components

#### Configure NGINX web server
[NGINX](https://nginx.org/en/docs/beginners_guide.html#conf_structure) is already configured to serve php content through PHP-FPM, though you can change the configuration file that is located in `./config/nginx.conf`.

The configuration tells NGINX to route the request to index.php which is interpreted using php-fpm service that is running using port 9000.

#### 4. Start the application with Docker

**Run the application in detached mode**:
```shell
docker compose up -d
```
Docker compose start the services defined in `docker-compose.yml`: php-fpm, web and db-mysql.

For each service that is defined in the configuration file, a container will be created. MySQL database is persisted using a volume which is created the first time that the docker application is started. Check the documentation for details.

**To check if the application is running correctly using**:
```shell
docker ps
```
you should see three containers running the three services.

**To stop the application run**:
```shell
docker compose down
```

### Dockerization
The `docker-compose.yml` is used to create a dev environment with PHP-8.2 FPM, NGINX and MySQL server.

> Notice `./docker/php-fpm/Dockerfile` used to create a php-fpm image in `docker-compose.yml` php-fpm service. This step is necessary to build a php-fpm image that has the required extensions.

## Project structure

### Entrypoint

The application entrypoint is the `app.php` that will bootstrap the PHP application.

### Routes

The application will start reading the file `routes.php`, containing all the available routes with their own `HTTP Verbs`.

Each `route` consist of three parts:

* `Method`

     The method that the route should respond, it can be:`POST,GET,PATCH or DELETE`.

* `URI`
    
    The *Uniform Resource Identifier* that the route should respond, it can be anything separated from `/`.

* `Action`

    The Action is what the route must do when called, it can be tree things:
    * `Closure` PHP Anonymous Function
    * `String` Simple String
    * `Array` Must have the association `use` pointing to an existing `Controller` and `method` separated by `@`.

The `Closure` or `Method` will receive as first argument an instance of `Request`.

```Php
Router::get('/',['use'=>'Controller@method']);

Router::post('/post/something',function(Request $request){
    /*Code goes here*/
});
```
When the `Request` object is ready, then the application will execute a `handle` method that will search for the requested `Route`, if there is any. If the Route is found, then the defined `Action` will be executed, passing as first parameter the `Request` object.

Those are the handling implementations in case the `Action` has been declared as:

* `String`

    It should get directly printed.

* `Closure`
    
    The Closure should be executed passing as first argument the `Request` object.

* `Array`
    
    The specified `Method` in the specified `Controller` should be called, passing as first argument the `Request` object.
    

>If one of the routes have been wrongly written, the application should not start.

#### Wild card
A `Route` can have an infinite amount of wild cards in order to facilitate the defining URIs. A wild card is created using mustache syntax `{id}`.

```php
Router::delete('/user/{id}',['use'=>'UserController@delete']);
```
This declared `Route` will match a `DELETE` request `http://localhost/user/10`.

Each wild card will be injected as argument to the defined `Closure` or `Controller`.

```php
Router::delete('/user/{id}',function(Request $request,$id){
    Users::delete('id',$id);
});
```

### Request

Once the `routes` have been parsed and validated, the application will capture the incoming request and save all the information in the `Request` instance.

The `Request` instance contains the information regarding the incoming request, represented with `Parameter` instances.

The `Request` instance has the following accessible properties.


```Php
public $method; // Request method | String
public $attributes; //The request attributes parsed from the PATH_INFO | Parameter
public $request; //Request body parameters ($_POST). | Parameter
public $query; //Query string parameters ($_GET). | Parameter
public $server; //Server and execution environment parameters ($_SERVER). | Parameter
public $files; //Uploaded files ($_FILES). | Parameter
public $cookies; //Cookies ($_COOKIE). | Parameter
public $headers; //Headers (taken from the $_SERVER). | Parameter
public $content; //The raw Body data | String
```
### Parameter

Each `Parameter` instance will have the following accessible methods:`all`,`keys`,`replace`,`add`,`get`,`set`,`has`,`remove`.

```Php
public function all(): array //Returns the parameters.
public function keys(): array //Returns the parameter keys.
public function replace(array $parameters = array()) //Replaces the current parameters by a new set.
public function add(array $parameters = array()) //Add parameters.
public function get(string $key, $default = null) //Returns a parameter by name, or fallback to default.
public function set(string $key, $value) //Sets a parameter by name.
public function has(string $key): bool //Returns true if the parameter is defined.
public function remove(string $key) //Removes a parameter.
```

```Php
/* http://localhost/info?beans=10 */
Router::get('/info',function(Request $request){
    return $request->query->has('beans') ?
        json_encode($request->query->get('beans')) : [];   
});
```
### Controller

Route requests are managed by `Controllers`. A `Controller` extend the `Controller` class and should look like this.

```php
class CustomController extends Controller{
    public function index(Request $request){ 
        /*Code goes here*/      
        return json_encode($request->content);
    }   
} 
```

> A `Controller` that is specified in `routes.php` must exists, otherwise the application will not start.

### Response</a>

If the Controller returns a json or text , you can use the `Response` instance. 

It will set the correct `Content-type` and return the desired data format, currently the implemented have the following `Content-types`:

* `application/json` with `json` static method.
* `text/plain`  with `text` static method.

```Php
/**
 * Return the desired HTTP code with json
 */
public static function json($content=[],int $status=self::HTTP_OK,$flags=JSON_FORCE_OBJECT|JSON_NUMERIC_CHECK)
/**
 * Return the desired HTTP code with text
 */
public static function text(string $content='',int $status=self::HTTP_OK)
/**
 * Return the desired HTTP code
 */
public static function code(int $status=self::HTTP_OK)
```


### View

If the `Controller` returns a Web page, then the `View` class  should be `required` and returned as response with the desired data.

```Php
class CustomController extends Controller{
    public function index(Request $request){
        $keys = $request->query->keys();
        return new View('home.php',compact('keys'));
    }
}
```

### ORM

The abstract `Model` class allows the mapping of Relational Database to Objects without the need to parse data manually.

Subclasses of Model can perform basic `CRUD` operations and access the entity relationship as simple as calling a method.

> Those operations are provided using the `Database` class from the `Query Builder Project`.

The `Model` must support `One to One`, `One to Many` and `Many to Many` relationships.

### Model

#### Methods
```php
/**
 * Create a one to one relationship
 * @param The Entity name is the class name to map the results to
 * @returns The Model The one to one entity for the current instance
 * */
public function hasOne(EntityName)
```

---

```php
/**
 * Creates a one to many relationship
 * @param The Entity name is the class name to map the results to
 * @returns An array of the one to many entities for the current instance
 * */
public function hasMany(EntityName)
```

---

```php
/**
 * Commits to database the current changes to the entity
 * @returns bool Success status
 * */
public function save():bool
```

---


```php
/*E.g.*/
$todo = Todo::first('id', 1);
$todo->title = "New title";
$todo->save(); 
```

---

```php
/**
 * Deletes the current instance from the database
 * @returns bool Success status
 * */
public function delete():bool
```

```Php
/*E.g.*/
$todo = Todo::first('id', 1);
$todo->delete();
```

### Static Methods

```Php
/** Configures the database connection */
public static function configConnection($host, $dbName, $user, $password)
```

```php
/*E.g.*/
Model::configConnection(
    'host',
    'db_name',
    'user',
    'pass'
); 
```

---

```Php
/** Deletes from the database using the input condition */
public static function destroy($col1, $exp, $col2): bool
```

```Php
/*E.g.*/
Todo::destroy('id', '=', 1);
```

---

```Php
/** 
 * Query the first result of a table using column value pair
 * @returns Model of first query result  
 */
public static function first($col1, $col2)
```

```Php
/*E.g.*/
Todo::first('id', 1);
```

---

```Php
/** 
 * Query the entity result of a table using expression
 * @returns An array of Model from the query result
 */
public static function find($col1, $col2)
```

```Php
/*E.g.*/
Todo::find('id', 1);
```

---

```Php
/** 
 * Query all the table values
 * @returns An array of mapped entities
 */
public static function all()
```

```Php
/*E.g.*/
Todo::all();
```

---

```Php
/** 
 * Applies a where condition to the table
 * @returns Statement Query Statement using the Model table
 */
public static function where($col1, $exp, $col2)
```

```Php
/*E.g.*/
Todo::where('title', '=', 'My title')->get()
```

---

```Php
/** 
 * The whereRaw method can be used to inject a raw "where" clause into your query.
 * @returns Statement Query Statement using the Model table
 */
public static function whereRaw($str)
```

```Php
/*E.g.*/
Todo::whereRaw("title = 'My title'")->get()
```

---

```Php
/** 
 * The whereIn method verifies that a given column's value is contained within the given array
 * @returns Statement Query Statement using the Model table
 */
public static function whereIn($col, $values)
```

```Php
/*E.g.*/
Todo::whereIn('id', [1,2,3])->get()
```

---

```Php
/** 
 * The whereNotIn method verifies that the given column's value is not contained in the given array
 * @returns Statement Query Statement using the Model table
 */
public static function whereNotIn($col, $values)
```

```Php
/*E.g.*/
Todo::whereNotIn('id', [1,2,3])->get()
```

---

```Php
/** 
 * The whereNull method verifies that the value of the given column is NULL
 * @returns Statement Query Statement using the Model table
 */
public static function whereNull($col)
```

```Php
/*E.g.*/
Todo::whereNull('updated_at')->get()
```

---

```Php
/** 
 * The whereNotNull method verifies that the column's value is not NULL
 * @returns Statement Query Statement using the Model table
 */
public static function whereNotNull($col)
```

```Php
/*E.g.*/
Todo::whereNotNull('updated_at')->get()
```

---

```Php
/** 
 * The whereBetween method verifies that a column's value is between two values
 * @returns Statement Query Statement using the Model table
 */
public static function whereBetween($col, $value1, $value2)
```

```Php
/*E.g.*/
Todo::whereBetween('day', 1, 5)->get()
```

---

```Php
/** 
 * The whereBetween method verifies that a column's value is not between two values
 * @returns Statement Query Statement using the Model table
 */
public static function whereNotBetween($col, $value1, $value2)
```

```Php
/*E.g.*/
Todo::whereNotBetween('day', 1, 5)->get()
```

---

```Php
/** 
 * The whereColumn method may be used to verify that two columns are equal
 * @returns Statement Query Statement using the Model table
 */
public static function whereColumn($col, $value1, $value2)
```

```Php
/*E.g.*/
Todo::whereColumn('first_name', 'last_name')->get()
```


```Php
/** 
 * You may also pass a comparison operator to the whereColumn method
 */
```

```Php
/*E.g.*/
Todo::whereColumn('updated_at', '>', 'created_at')->get()
```

```Php
/** 
 * You may also pass an array of column comparisons to the whereColumn method. 
 * These conditions will be joined using the 'and' operator.
 */
```

```Php
/*E.g.*/
Todo::whereColumn([
        ['first_name', '=', 'last_name'],
        ['updated_at', '>', 'created_at'],
    ])->get()
```

---

```Php
/** 
 * Creates a new value in the database using data array (column + value)
 * @returns Model The new instance on success
 */
public static function create(array $data)
```

```Php
/*E.g.*/
Todo::create(['title' => 'My title']);
```

---

```Php
/** 
 * Select columns from table (if $columns array is empty select automatically all the columns)
 * @returns Statement Select Statement
 */
public static function select(array $columns=[])
```

```Php
/*E.g.*/
Todo::select()->get()
```

### Join Methods

```Php
/**
* Inner join
* @returns Statement Query Statement using the Model table
*/
public function innerJoin($modelClassName, string $col1, string $exp, string $col2, $and=false)
```
```Php
/*E.g.*/
Article::select(['author.id', 'article.id'])->innerJoin(Author::class,"author_id","=","id")->get();
```
---
```Php
/**
* Cross join
* @returns Statement Query Statement using the Model table
*/
public function crossJoin($modelClassName, $and=false)
```
```Php
/*E.g.*/
Article::select(['author.id', 'article.id'])->crossJoin(Author::class)->get();
```
---
```Php
/**
* Left join
* @returns Statement Query Statement using the Model table
*/
public function leftJoin($modelClassName, string $col1, string $exp, string $col2, $and=false)
```
```Php
/*E.g.*/
Article::select(['author.id', 'article.id'])->leftJoin(Author::class,"author_id","=","id")->get();
```
---
```Php
/**
* Right join
* @returns Statement Query Statement using the Model table
*/
public function rightJoin($modelClassName, string $col1, string $exp, string $col2, $and=false)
```
```Php
/*E.g.*/
Article::select(['author.id', 'article.id'])->rightJoin(Author::class,"author_id","=","id")->get();
```
---
```Php
/**
* Full join example
*/
public function fullJoin($modelClassName, string $col1, string $exp, string $col2, $and=false)
```
```Php
/*E.g.*/
Article::select(['author.id', 'article.id'])->fullJoin(Author::class,"author_id","=","id")->get();
```

### Multiple WHERE Clauses
```Php
/**
* Make attention ALL not static where methods has one more parameter $and to use AND operator
* public function where($col1, $exp, $col2, $and=false)
* public function whereRaw($str, $and=false)
* public function whereIn($col, $values, $and=false)
* public function whereNotIn($col, $values, $and=false)
* public function whereNull($col, $and=false)
* public function whereNotNull($col, $and=false)
* public function whereBetween($col, $value1, $value2, $and=false)
* public function whereNotBetween($col, $value1, $value2, $and=false)
*/
Todo::where('title','=','MyTitle')->where('genre', '=', 'mystery', true)->get();
```
---
```Php
/**
* To use multiple where clauses with OR operator
* public function orWhere($col1, $exp, $col2)
* public function orWhereRaw($str)
*/
Todo::where('title','=','MyTitle')->orWhere('genre', '=', 'mystery')->get();

Todo::where('title','=','MyTitle')->orWhereRaw("genre = 'mystery'")->get();
```



#### Relationship

To enable relationship mapping, create a method to return the entities and use the following methods allow to map the
entity relationship to the current instance. The methods take the `className` as parameter.

`One to One`

```Php
class Article { //The author of an article
    public function author() {
        return $this->hasOne('User');
    }
}
```

`One to Many` and `Many to Many`

```Php
class User { //The author articles
    public function articles() {
        return $this->hasMany('Articles');
    }
}
```

#### Configuration

Before making any queries it is necessary to setup database configuration using static function ```configConnection```.
```Php
Model::configConnection('host', 'dbName', 'user', 'password');
```

> Model class cannot be instantiated as it is, but must be extended by another class

>
> ##### <a name="caveats">Caveats</a>
> * The table name is inferred from the class `ClassName` to table `class_name` by converting from `PascalCase` to `snake_case`.
> * Foreign keys must follow the `<table>_id` name convention.
> * Entity tables must have an `id` field.
> * In order to work, mapped tables must have an `id` column

`Extend Model` to map an existing table to the class.

```Php
class Author extends Model {
    /*Code goes here*/
}
```

Class `Author` is mapped to a table named `author`.
Now static and instance methods can be called on `Author`.

```Php
// Create a new record in author table
$author = Author::create([
    // id is not specified as it has auto increment
    'name' => 'Satoshi',
    'username' => 'john123'
]);

// Update
$author->name = 'Nakamoto';
$author->save();

// Delete
$author->delete();
// or
Author::delete('name', '=', 'John');
```

### Database Schema

```MySQL
CREATE TABLE tweet (
   id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   content VARCHAR(280) NOT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE comment (
     id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     tweet_id INT(6) UNSIGNED,
     content VARCHAR(280) NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     FOREIGN KEY (tweet_id) REFERENCES tweet(id) ON DELETE NO ACTION ON UPDATE NO ACTION
);
```
