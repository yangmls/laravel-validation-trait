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

    public function ruleMessages()
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

    public function ruleMessages()
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

### Inline Validators

validator can be defined in the class and will be called automatically 

```php
use Yangmls\ValidationTrait;

class User extends Eloquent
{
    use ValidationTrait;

    protected function validatorEmail($value, $input, $options)
    {
        // $value is attribute value
        // $input is whole input
        // $options is the config you pass to saveFromRequest
        
        // Note: 
        // 1. you must use addError to stop saving
        // 2. you must return true if you think the validator is passed
    }
}

```

### Built-in hooks

below methods are called automatically when you do validating or saving

`beforeSave`, `afterSave`, `beforeValidate`, `afterValidate`

however you can also use laravel built-in events for saving/creating/updating

### License

under the [MIT license](http://opensource.org/licenses/MIT)