# About my mvc Framework



## How to install
```git
git clone https://github.com/RaoulvanWijk/mvc.git
```
after you cloned the repository

you need to run composer install

to install all the packages
## Running the framework
To run the framework you have 2 options:
Run it from command line
```bash
$ php mvc serve
```
or run it by creating a virtual host

**And make sure you are running your database if you want to use it**


## Routing
Go to the web.php file in the routes folder
```php
use App\Http\Route;
use App\Http\Controllers\DemoController;

Route::get('url', [DemoController:class, 'method'], name);
Route::post('url', [DemoController:class, 'method'], name);
Route::put('url', [DemoController:class, 'method'], name);
Route::delete('url', [DemoController:class, 'method'], name);
```

### Grouped routes
```php
Route::prefix('/demo')->group(function() {
  	Route::get('url', [DemoController:class, 'method'], name);
});
```

## Controllers
A controller is used to communicate between a view and a model

### Creating a controller
You can create a controller by using the following command:
```bash
$ php mvc make:controller YoureNameOfTheController
```

### Routing in a controller

```php
class DemoController extends Controller
{
  public function demo()
  {
    // Redirect to different route
    $this->redirect('name_of_route');

    // Redirect with a message
    $this->redirect('name_of_route')->with('key', 'value');

    // Go to previous route
    $this->back();
  }
}
```

### Rendering a view
```php
class DemoController extends Controller
{
  public function demo()
  {
    $this->view('views/file.php');
  }
}
```

## Models
A model is used to interact with the database

### Creating a Model
You can create a model by using the following command:
```bash
$ php mvc make:model YoureNameOfTheModel
```
The name you give to the model wil also be parsed to the name of your database table
for example: 
```php
class User
```
will by default have **users** as table name

You can change the name of databaseTable by adding
```php
protected $databaseTable = 'tablename';
```

### Model properties

Below a list of properties that you can change
```php
   /**
   * Var used to store the database Table of the model
   */
  protected string $databaseTable;

  /**
   * Var used to keep track of tables primary key
   */
  protected string $primaryKey = 'id';

  /**
   * Var used to store all the columns that are allowed to be inserted
   */
  protected array $fillable = [];
```

### Model Query Builder
This model also has a query builder wich you can use
example below

```php
  public function Demo()
  {
  // Using not Raw functions are **NOT SAFE** using this without sanitizing the user input is not recommended
    return Progress::select()->where('id = 1')->get();
    
    // using Raw functions are safer since it is using prepared statements with binding
    return Progress::select()->whereRaw('id = :id', [':id', 1])->get();
    
    // But you should still always sanitize the user input
  }
```

At the end of every builded query you need to use
```php
->get();
```
to execute the sql statement

## Requests
In this framework you can use a request class to validate request and request data

### Creating a request class
To create a request class use the following command
```bash
$ php mvc make:request NameOfRequestClass
```

## Specifying the rules
To validate the data you need to use the `rules()` function as following:

```php
  public function rules()
  {
    return [
      'username' => 'required|string|min:8',
      'password' => 'required|string|min:8'
    ];
  }
```

## using `authorize()`

At the moment there is no middleware so you can use the `authorize()` functions to act as middleware

and authorize needs to return true to validate the request otherwise it will throw an error
```php
  public function authorize()
  {
    return true;
  }
```
