Laravel Validation Trait
================================================

Provide a powerful trait for laravel 4 model

### Use it in Eloquent

create a model and use it

```php
use Yangmls\ValidationTrait;

class User extends Eloquent
{
    use ValidationTrait;

    public function rules()
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function rulesMessages()
    {
        return [
            'email.required' => ':attribute is required',
        ];
    }

    public function customAttributes()
    {
        return [
            'email' => 'E-Mail',
        ];
    }
}

```

create a new model and save

```php
$model = new User();
$result = $model->saveFromRequest(Input::all()); // true or false
return Response::json($model->getErrors());

```

create a new model without errors

```php
$model = User::createFromRequest(Input::all()); // User instance or null

```

save a existing model

```php
$model = User::find(1);
$result = $model->saveFromRequest(Input::all()); // true or false
return Response::json($model->getErrors());

```

### Use it in other models

sometimes you may process a form without Eloquent, you can do like this

```php
use Yangmls\ValidationTrait;

class Login
{
    use ValidationTrait;

    public $attributes;

    public function __construct($input = [])
    {
        $this->attributes = $input;
    }
    
    public function validate($options = [])
    {
        return $this->validateRequest($this->attributes, $options);
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function rulesMessages()
    {
        return [
            'email.required' => ':attribute is required',
        ];
    }

    public function customAttributes()
    {
        return [
            'email' => 'E-Mail',
        ];
    }
}

```

then call it in controller

```php
$model = new Login(Input::all());
$result = $model->validate(); // true or false
return Response::json($model->getErrors());

```

### Built-in hooks

below methods are called automatically when you do validating or saving

`beforeSave`, `afterSave`, `beforeValidate`, `afterValidate`

however you can use laravel built-in events to do this too

### License

under the [MIT license](http://opensource.org/licenses/MIT)