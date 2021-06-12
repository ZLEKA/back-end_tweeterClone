# MVC

A custom PHP 7 application created side-by-side with [Lambert Mata](https://github.com/LambertMata) in order to help interns become familiar with the MVC design pattern.

---

* [Important Remarks](#important-remarks)
* [How do I start the application?](#start)
* [How does it work ?](#hdiw)
    * [Routes](#routes)
        * [White card](#white-card)
    * [Response](#response)
    * [Request](#request)
        * [Parameter](#parameter)
    * [Controller](#controller)
    * [View](#view)
    * [ORM](#orm)
        * [Configuration](#model-config)
            * [Caveats](#caveats)
        * [Model](#model)
            * [Static Methods](#static)
        * [Relationships](#relationship)
  * [Database Schema](#db-schema)

## <a name="important-remarks">Important Remarks</a>

### Vagrantfile

The current `Vagrantfile` have the [ncaro/php7-debian8-apache-nginx-mysql](https://app.vagrantup.com/ncaro/boxes/php7-debian8-apache-nginx-mysql) box.

### Web Server

The Web Server must be configured to route every request to `index.php` file present in the root `code` folder. If you are using the `Vagrantfile` here the `nginx` sample:
```txt
location / {
    # Route all the URIs to index.php
    try_files /index.php?$args $uri /index.php?$args;
    ...
}
```
### Database

In order to use the `MySQL` database, you must change the root password.

```sql
SET PASSWORD = PASSWORD('xxxxxxxx');
```

Since the application will search fot a database called `app`, you must create one.

```sql
CREATE DATABASE app;
```

## <a name="start">How do I start the application?</a>

The application will start once the file `app.php` is included.

## <a name="hdiw">How does it work ?</a>

### <a name="routes">Routes</a>

The application will start reading the file `routes.php`, containing all the available routes with their own `HTTP Verbs`.

Each `route` consist of three things:

* `Method`
    * The method that the route should respond, it can be:`POST,GET,PATCH or DELETE`.

* `URI`
    * The *Uniform Resource Identifier* that the route should respond, it can be anything separated from `/`.

* `Action`
    * The Action is what the route must do when called, it can be tree things:
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
When the `Request` object is ready, then the application will execute a `handle` method that will search for the requested `Route`, if there is any. If the Route is found, then the defined `Action`
will be executed, passing as first parameter the `Request` object.

Those are the handling implementations in case the `Action` has been declared as:

* `String`
    *  It should get directly printed.

* `Closure`
    *  The Closure should be executed passing as first argument the `Request` object.

* `Array`
    *  The specified `Method` in the specified `Controller` should be called, passing as first argument the `Request` object.
    

>If one of the routes have been wrongly written, the application should not start.

#### <a name="white-card">White card</a>
A `Route` can have an infinite amount of white cards in order to facilitate the URI implementation. The white card is created using a mustached syntax `{id}`.

```php
Router::delete('/user/{id}',['use'=>'UserController@delete']);
```
This declared `Route` will match a `DELETE` request `http://localhost/user/10`.

Each white card will be added as argument to the defined `Closure` or `Controller`.

```php
Router::delete('/user/{id}',function(Request $request,$id){
    Users::delete('id',$id);
});
```

### <a name="request">Request</a>

Once the `routes` have been parsed and validated, the application will capture the incoming request and save all the information in the `Request` instance.

The `Request` instance should contain all the information regarding the incoming request,correctly separated and categorized in `Parameter` instances.

The `Request` instance will have the following accessible properties.


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
### <a name="response">Response</a>

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

### <a name="parameter">Parameter</a>

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
### <a name="controller">Controller</a>

A custom `Controller` must be created in order to properly work ,it should look like this.

```php
class CustomController extends Controller{
    public function index(Request $request){ 
        /*Code goes here*/      
        return json_encode($request->content);
    }   
} 
```

>If a `Controller` is specified in the `routes.php` file and not found, the application should not start.

### <a name="view">View</a>

If the `Controller` returns a Web page, then the `View` class  should be `required` and returned as response with the desired data.

```Php
class CustomController extends Controller{
    public function index(Request $request){
        $keys = $request->query->keys();
        return new View('home.php',compact('keys'));
    }
}
```

### <a name="orm">ORM</a>

The abstract `Model` class allows the mapping of Relational Database to Objects without the need to parse data manually.

Subclasses of Model can perform basic `CRUD` operations and access the entity relationship as simple as calling a method.

> Those operations are provided using the `Database` class from the `Query Builder Project`.

The `Model` must support `One to One`, `One to Many` and `Many to Many` relationships.

### <a name="model">Model</a>

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

### <a name="static">Static Methods</a>

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
Todo::where('title', 'My title')->get()
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

#### <a name="relationship">Relationship</a>

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

#### <a name="model-config">Configuration</a>

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

### <a name="db-schema">Database Schema</a>

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
