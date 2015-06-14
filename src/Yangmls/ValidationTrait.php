<?php
namespace Yangmls;

use Illuminate\Support\MessageBag,
    Validator;

/**
 * Validation Trait that provides methods to create and update model easily
 */
trait ValidationTrait
{
    /**
     * message bag
     *
     * @var \Illuminate\Support\MessageBag 
     */
    protected $errors;

    /**
     * create model by request data
     * 
     * @param array $input
     * @param array $options
     * @return null|static
     */
    public static function createFromRequest($input = [], $options = [])
    {
        $model = new static();
        return $model->saveFromRequest($input, $options) ? $model : null;
    }
    
    /**
     * update model by request data
     * 
     * @param array $input
     * @param array $options
     * @return boolean
     */
    public function updateFromRequest($input = [], $options = [])
    {
        return $this->saveFromRequest($input, $options);
    }
    
    /**
     * save data by request data
     * 
     * @param array $input
     * @param array $options
     * @return boolean
     */
    public function saveFromRequest($input = [], $options = [])
    {
        if(!$this->validateRequest($input, $options)) {
            return false;
        }
        foreach($input as $key=>$value) {
            $this->setAttribute($key, $value);
        }
        
        if(method_exists($this, 'beforeSave')) {
            if(!$this->beforeSave($options)) {
                return false;
            }
        }
        $beforeExists = $this->exists;
        $saved = $this->save();
        $afterExists = $this->exists;
        
        if($saved && method_exists($this, 'afterSave')) {
            $this->exists = $beforeExists;
            $this->afterSave($options);
            $this->exists = $afterExists;
        }

        return $saved;
    }
    
    /**
     * validate current model by request data
     * 
     * @param array $input
     * @param array $options
     * @return boolean
     */
    public function validateRequest(&$input, $options = [])
    {
        if(method_exists($this, 'beforeValidate')) {
            if(!$this->beforeValidate($input, $options)) {
                return false;
            }
        }
        
        $rules = method_exists($this, 'rules') ? $this->rules() : [];
        $ruleMessages = method_exists($this, 'ruleMessages') ? $this->ruleMessages() : [];
        $customAttributes = method_exists($this, 'customAttributes') ? $this->customAttributes() : [];
        
        $keys = [];
        
        //defined in rules
        if(!empty($rules)) {
            $validation = Validator::make($input, $rules, $ruleMessages, $customAttributes);
            $this->getErrors(true)->merge($validation);
            foreach(array_keys($rules) as $key) {
                if(!$validation->errors()->has($key)) {
                    $keys[] = $key;
                }
            }
        }
        
        //inline validators
        foreach($input as $key=>$value) {
            $validator = 'validator' . ucfirst($key);
            if(!method_exists($this, $validator)) {
                continue;
            }
            if($this->$validator($value, $input, $options)) {
                $keys[] = $key;
            }
        }
        $errors = $this->getErrors(true);
        //you can pass ignore as option, so you will get input with passed keys
        if(!$errors->isEmpty() && !array_get($options, 'ignore')) {
            return false;
        }
        $fillable = property_exists($this, 'fillable') ? $this->fillable : [];
        $fillable = array_merge($fillable, $keys);
        $input = array_only($input, $fillable);
        
        if(method_exists($this, 'afterValidate')) {
            if(!$this->afterValidate($input, $options)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * add error message to message bag
     * 
     * @param string $key
     * @param string $message
     */
    public function addError($key, $message)
    {
        $this->getErrors(true)->add($key, $message);
    }
    
    /**
     * get all messages from message bag
     * 
     * @param boolean $raw
     * @return mixed
     */
    public function getErrors($raw = false)
    {
        if($this->errors === null) {
            $this->errors = new MessageBag();
        }
        
        if($raw) {
            return $this->errors;
        }
        return $this->errors->getMessages();
    }
    
    /**
     * get single error message from message bag
     * 
     * @param string $key
     * @return string
     */
    public function getError($key = null)
    {
        $errors = $this->getErrors(true);
        return $errors->first($key);
    }
    
    /**
     * if message bag has errors
     * 
     * @return boolean
     */
    public function hasErrors()
    {
        $errors = $this->getErrors(true);
        return !$errors->isEmpty();
    }
}