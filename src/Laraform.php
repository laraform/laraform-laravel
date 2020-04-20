<?php

/*!
 *  Laraform
 *  License: https://github.com/laraform/laraform-laravel/blob/master/LICENSE
 *  Copyright (c) 2018-2019 Adam Berecz <adam@laraform.io>
 */

namespace Laraform;

use Laraform\Authorization\AuthorizationBuilder;
use Laraform\Elements\Factory as ElementFactory;
use Laraform\Event\Event;
use Laraform\Validation\Validation;
use Laraform\User\UserBuilder;
use Laraform\Support\Arr;
use Laraform\Database\DatabaseBuilder;
use Laraform\Support\Hash;

class Laraform implements \JsonSerializable
{
	/**
	 * Schema of form
	 *
	 * @var array
	 */
	public $schema;

	/**
	 * Vue component to render
	 *
	 * @var array
	 */
	public $component = 'laraform';

	/**
	 * Vuex store (state) path
	 * 
	 * @var string
	 */
	public $storePath;

	/**
	 * Name of model class
	 * 
	 * Eg: App\User::class
	 * 
	 * @var string
	 */
	public $model;

	/**
	 * Name of primary key
	 *
	 * @var string
	 */
	public $primaryKey = 'id';

	/**
	 * Theme for the form
	 *
	 * @var array
	 */
	public $theme;

	/**
	 * Column sizes definition
	 *
	 * @var array
	 */
	public $columns = null;

	/**
	 * CSS class of form
	 *
	 * @var string
	 */
	public $class;

	/**
	 * Theme classes override
	 *
	 * @var array
	 */
	public $classes;

	/**
	 * Whether label should render for elements
   * without label defined
	 *
	 * @var boolean
	 */
	public $labels;

	/**
	 * Whether form errors should be displayed
   * above the form
	 *
	 * @var boolean
	 */
	public $formErrors;

	/**
	 * Form layout
	 *
	 * @var string
	 */
	public $layout;

	/**
	 * Form buttons
	 *
	 * @var array
	 */
	public $buttons;

	/**
	 * Additional "data" props to set for the form.
	 *
	 * @var array
	 */
	public $with = [];

	/**
	 * Overwrite default validation messages
	 *
	 * Eg.:
	 * [
	 * 	'required' => 'This field is required'
	 * ]
	 * 
	 * @var bool
	 */
	public $messages = [];

	/**
	 * Locale of the form
	 * 
	 * Defaults to app locale
	 *
	 * @var string
	 */
	public $locale;

	/**
	 * Entpoint to where the form will be submitted
	 * 
	 * Default: config('laraform.endpoint')
	 *
	 * @var string
	 */
	public $endpoint;

	/**
	 * Method how the form should be submitted
	 * 
	 * Default: config('laraform.method')
	 *
	 * @var string
	 */
	public $method;

	/**
	 * Validated form on these events
	 * 
	 * Default: config('laraform.validateOn')
	 *
	 * @var string
	 */
	public $validateOn;

	/**
	 * Name of attribute on User model which returns the roles
	 *
	 * @var string
	 */
	public $rolesAttribute;

	/**
	 * Auth guard to use
	 *
	 * @var string
	 */
	public $guard;

	/**
	 * List of permissions
	 *
	 * @var array
	 */
	public $permissions = [];

	/**
	 * Key of current entity
	 *
	 * @var integer
	 */
	protected $key;

	/**
	 * Determine if form is invalid
	 *
	 * @var boolean
	 */
	protected $invalid = false;

	/**
	 * Determine if form has been validated
	 *
	 * @var boolean
	 */
	protected $validated = false;

	/**
	 * Current data
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Contains any data that has been generated
   * during processing data, like new IDs. Can
   * be used to forward to the frontend to up-
   * date values.
	 *
	 * @var array
	 */
	protected $updates = [];

	/**
	 * Current elements
	 *
	 * @var Laraform\Contracts\Elements\Element[]
	 */
	protected $elements = [];

	/**
	 * Database instance
	 *
	 * @var Laraform\Contracts\Database\Database
	 */
	protected $database;

	/**
	 * Authorization instance
	 *
	 * @var Laraform\Contracts\Authorization\Authorization
	 */
	protected $authorization;

	/**
	 * User instance
	 *
	 * @var Laraform\Contracts\User\user
	 */
	protected $user;

	/**
	 * Validation instance
	 *
	 * @var Laraform\Contracts\Validation\Validation
	 */
	protected $validation;

	/**
	 * Event dispatcher instance
	 *
	 * @var Laraform\Contracts\Event\Dispatcher
	 */
	protected $event;

	/**
	 * Element factory instance
	 *
	 * @var Laraform\Elements\Factory
	 */
	protected $elementFactory;

	/**
	 * Return new Laraform instance
	 *
	 * @param AuthorizationBuilder $authorizationBuilder
	 * @param UserBuilder $userBuilder
	 * @param Validation $validation
	 * @param Event $event
	 * @param ElementFactory $elementFactory
	 * @param DatabaseBuilder $databaseBuilder
	 */
	public function __construct(
		AuthorizationBuilder $authorizationBuilder,
		UserBuilder $userBuilder,
		Validation $validation,
		Event $event,
		ElementFactory $elementFactory,
		DatabaseBuilder $databaseBuilder
	) {
		if (method_exists($this, 'boot')) {
			app()->call([$this, 'boot']);
		}

		$this->setSchema();

		$this->database = $databaseBuilder
			->setForm($this)
			->build();

		$this->validation = $validation;
		$this->event = $event;
		$this->elementFactory = $elementFactory;

		$this->setElements();
	}

	/**
	 * Validate form
	 *
	 * @return void
	 */
	public function validate()
	{
		$this->fire('validating');

		$this->validation->validate($this->elements, $this->messages);

		$this->validated = true;

		if ($this->validation->fails()) {
			$this->invalid = true;
		}

		$this->fire('validated');
	}

	/**
	 * Load data to form
	 *
	 * @param array|integer $data
	 * @return bool
	 */
	public function load($data)
	{
		if (!in_array(gettype($data), ['integer', 'array'])) {
			throw new \InvalidArgumentException('Invalid argument type: ' . gettype($data));
		}

		if (is_numeric($data)) {
			return $this->loadByKey($data);
		}

		$this->fire('loading');

		$this->setData($data);

		$this->fire('loaded');

		return $this;
	}

	/**
	 * Load data by key
	 *
	 * @param integer $key
	 * @return bool
	 */
	public function loadByKey($key)
	{
		$this->setKey($key);

		$this->fire('loading');

		$this->setData($this->database->load($key));

		$this->fire('loaded');

		return $this;
	}

	/**
	 * Save data
	 *
	 * @return void
	 */
	public function save()
	{
		$this->fire('saving');

		$this->hasKey() ? $this->update() : $this->insert();

		$this->fire('saved');

		return true;
	}

	/**
	 * Insert new data
	 *
	 * @return void
	 */
	private function insert()
	{
		$this->fire('inserting');

		$this->database->insert($this->data);

		$this->setKeysFromInserted();
		$this->addKeysToUpdates();

		$this->fire('inserted');
	}

	/**
	 * Update data
	 *
	 * @return void
	 */
	private function update()
	{
		$this->fire('updating');

		$this->database->update($this->data, $this->key);
		$this->addKeysToUpdates();

		$this->fire('updated');
	}

	/**
	 * Renders form DOM element
	 *
	 * @return string
	 */
	public function render() {
    $model = $this->storePath ? "store-path=\"{$this->storePath}\"" : '';
    $form = ":form=\"{$this->toProp()}\"";

		return "<{$this->getComponent()} $form $model></{$this->getComponent()}>";
	}

	/**
	 * Transforms form to Vue property
	 *
	 * @return string
	 */
	public function toProp() {
		return htmlspecialchars(json_encode($this, true));
	}

	/**
	 * Add an event listener
	 *
	 * @param string $event
	 * @param callable $callback
	 * @return void
	 */
	public function on($event, $callback)
	{
		$this->event->listen($event, $callback);
	}

	/**
	 * Make a callable method from method name
	 *
	 * @param string $method
	 * @return callable
	 */
	protected function makeCallableMethod($method)
	{
		return function () use ($method) {
			return $this->$method();
		};
	}

	/**
	 * Fire an event
	 *
	 * @param string $event
	 * @return any|void
	 */
	public function fire($event)
	{
		if ($result = $this->event->fire($event)) {
      return $result;
    }

    if (method_exists($this, $event)) {
      if ($result = $this->$event()) {
        return $result;
      }
    }
	}

	/**
	 * Creates a fail response
	 *
	 * @param string $message
	 * @param array $payload
	 * @return void
	 */
	public function fail($message, $payload = [])
	{
		return response([
      'status' => 'fail',
      'messages' => [$message],
      'payload' => $payload
    ]);
	}

	/**
	 * Creates a success response
	 *
	 * @param string $message
	 * @param array $payload
	 * @return void
	 */
	public function success($message = null, $payload = [])
	{
    $messages = [];

    if ($message) {
      $messages[] = $message;
    }

		return response([
      'status' => 'success',
      'messages' => $messages,
      'payload' => $payload
    ]);
	}

	/**
	 * Set the value of data
	 *
	 * @param array $data
	 * @return void
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		$this->elements->setData($data);
	}

	/**
	 * Get the value of elements
	 *
	 * @return Laraform\Contracts\Elements\Element
	 */
	public function getElements()
	{
		return $this->elements;
	}

	/**
	 * Return the entity based on current key
	 *
	 * @return object
	 */
	public function getEntity()
	{
		return $this->hasKey() ? $this->database->find($this->key) : null;
	}

	/**
	 * Get the value of key
	 *
	 * @return integer
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Get the underlying validator instance
	 *
	 * @return Illuminate\Validation\Validator
	 */
	public function getValidator()
	{
		return $this->validation->validator->validator;
	}

	/**
	 * Set the value of key
	 *
	 * @param integer $key
	 * @return void
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * Determine if key is set
	 *
	 * @return boolean
	 */
	public function hasKey()
	{
		return !empty($this->key);
	}

	/**
	 * Determine if model is set
	 *
	 * @return boolean
	 */
	public function hasModel()
	{
		return !empty($this->model);
	}

	/**
	 * Determine if form is invalid
	 *
	 * @return boolean
	 */
	public function isInvalid()
	{
		return $this->invalid;
	}

	/**
	 * Determine if form is validated
	 *
	 * @return boolean
	 */
	public function isValidated()
	{
		return $this->validated;
	}

  public function getErrors() {
    return $this->validation->getErrors();
  }

	/**
	 * Set key from current data
	 *
	 * @return void
	 */
	public function setKeyFromData()
	{
		$key = isset($this->data[$this->primaryKey])
			? $this->data[$this->primaryKey]
			: null;

		if ($key !== null && !is_numeric($key)) {
			$key = Hash::decode($key);
		}

		$this->key = $key;
	}

	/**
	 * Extend data and set current key
	 * from last inserted values
	 *
	 * @return void
	 */
	public function setKeysFromInserted()
	{
		$keys = $this->database->getNewKeys();

		$this->data = Arr::mergeDeep($this->data, $keys);

		$this->key = $keys[$this->primaryKey] ?? null;
	}

	/**
	 * Add inserted keys to updated
	 *
	 * @return void
	 */
	public function addKeysToUpdates()
	{
		$keys = $this->database->getNewKeys();

		if (!empty($keys)) {
			$this->addToUpdates($keys);
		}
	}

  /**
   * Merges data with current updates
   *
   * @param array $updates
   * @return void
   */
  public function addToUpdates($updates) {
    $this->updates = Arr::mergeDeep($this->updates, $updates);
  }

  /**
   * Returns form udpates
   *
   * @return []
   */
  public function getUpdates() {
    return $this->updates;
  }

	/**
	 * Set schema
	 *
	 * @return void
	 */
	protected function setSchema()
	{
		if ($this->schema === null) {
			$this->schema = $this->schema();
		}
	}

	/**
	 * Set form elements
	 *
	 * @return void
	 */
	public function setElements()
	{
		$this->elements = $this->elementFactory->make(
			$this->schema,
			null, 
			$this->createElementOptions()
		);
	}

	/**
	 * Create options for element
	 *
	 * @return void
	 */
	protected function createElementOptions()
	{
		return [];
	}

	/**
	 * Set method
	 *
	 * @return Laraform
	 */
	public function setMethod($method)
	{
    $this->method = $method;

    return $this;
	}

	/**
	 * Set endpoint
	 *
	 * @return Laraform
	 */
	public function setEndpoint($endpoint)
	{
    $this->endpoint = $endpoint;

    return $this;
	}

	/**
	 * Returns roles attribute name
	 *
	 * @return string
	 */
	public function getRolesAttribute()
	{
		return $this->rolesAttribute ?: 'role';
	}

	/**
	 * Returns component name
	 *
	 * @return string
	 */
	public function getComponent()
	{
		return $this->component ?: config('laraform.component');
	}

	/**
	 * Generate the form key
	 *
	 * @return string
	 */
	public function getFormKey()
	{
		return encrypt((new \ReflectionClass($this))->getShortName());
	}

	/**
	 * Get transformed schema
	 *
	 * @param string $side
	 * @return array
	 */
	public function getSchema($side)
	{
		return $this->elements->getSchema($side);
	}

	/**
	 * Retrieving buttons
	 *
	 * @return array
	 */
	public function getButtons()
	{
		return method_exists($this, 'buttons')
      ? $this->buttons()
      : $this->buttons;
	}

	/**
	 * Retrieving messages
	 *
	 * @return array
	 */
	public function getMessages()
	{
		return method_exists($this, 'messages')
      ? $this->messages()
      : $this->messages;
	}

	/**
	 * Form theme
	 *
	 * @return string
	 */
	public function getTheme()
	{
		return $this->theme ?: config('laraform.theme');
	}

	/**
	 * Get form locale
	 *
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale ?: config('laraform.locale');
	}

	/**
	 * Get form default language
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language ?: config('laraform.language');
	}

	/**
	 * Available languages for form
	 *
	 * @return string
	 */
	public function getLanguages()
	{
		return $this->languages ?: config('laraform.languages');
	}

	/**
	 * Submission endpoint
	 *
	 * @return string
	 */
	public function getEndpoint()
	{
		return $this->endpoint ?: config('laraform.endpoint');
	}

	/**
	 * Submission method
	 *
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method ?: config('laraform.method');
	}
	
	/** 
	 * Returns validateOn
	 *
	 * @return string
	 */
	public function getValidateOn()
	{
		return $this->validateOn ?: config('laraform.validateOn');
	}
	
	/**
	 * Returns columns
	 *
	 * @return array
	 */
	public function getColumns()
	{
		return $this->columns ?: config('laraform.columns');
	}

	/**
	 * Retrieves labels
	 *
	 * @return string
	 */
	public function getLabels()
	{
		return $this->labels !== null ? $this->labels : config('laraform.labels');
	}

	/**
	 * Retrieves layout
	 *
	 * @return string
	 */
	public function getLayout()
	{
		return $this->layout ?: config('laraform.layout');
	}

	/**
	 * Retrieves formErrors
	 *
	 * @return string
	 */
	public function getFormErrors()
	{
		return $this->formErrors !== null ? $this->formErrors : config('laraform.formErrors');
	}

	/**
	 * Sets the 'with' property
	 *
	 * @return self
	 */
  public function with(array $with) {
    $this->with = $with;

    return $this;
  }

	/**
	 * Serializes form variables when json encoded
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		$form = [
			'key' => $this->getFormKey(),
			'data' => $this->data,
			'schema' => $this->getSchema('frontend'),
			'theme' => $this->getTheme(),
			'columns' => $this->getColumns(),
			'class' => $this->class,
			'classes' => $this->classes,
			'labels' => $this->getLabels(),
			'layout' => $this->getLayout(),
			'formErrors' => $this->getFormErrors(),
			'buttons' => $this->getButtons(),
			'with' => $this->with,
			'messages' => $this->getMessages(),
			'locale' => $this->getLocale(),
			'endpoint' => $this->getEndpoint(),
			'method' => $this->getMethod(),
			'validateOn' => $this->getValidateOn(),
		];

		return $form;
	}
}