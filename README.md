# hyperf-user-settings
Simple user settings util for hyperf Settings are stored as JSON in a single database column, so you can easily add it to an existing table (`users` for example).


## Installation
1. Run `composer require lysice/hyperf-user-settings` to include this in your project.
2. Run `php bin/hyperf.php vendor:publish lysice/hyperf-use-settings` to publish the config file and migration.
3. Run `php bin/hyperf.php migrate` to add field to your table. Alternatively, use the Laravel migration included in this package to automatically create a `settings` column in the `users` table: ` php bin/hyperf.php migrate`.
4. Modify the published configuration file located at `config/user-setting.php`.


## Configuration
There is a file `config/user-setting.php` to adjust package configuration. If this file doesn't exist, run `php bin/hyperf.php vendor:publish ` to create the default configuration file.

```php
return array(
  'table' => 'users',
  'column' => 'settings',
  'constraint_key' => 'id',
  'default_constraint_value' => null,
  'custom_constraint' => null,
);
```

#### Table
Specify the table on your database that you want to use.

#### Column
Specify the column in the above table that you want to store the settings JSON data in.

#### Constraint key
Specify the index column used for the constraint - this is used to differentiate between different users, objects or models (normally id).

#### Default constraint value
Specify the default constraint value - by default this will be the user's ID you need pass userId to the construct function, and will be superseded by specifying a `$constraint_value` on any function call.

#### Custom constraint
Specify a where clause for each query - set this if you **do not** want to access different rows (for example if your app is single-user only).


## Usage
Use the helper function `setting($userId)` to initial the Setting class, and you can invoke any function in Setting class. 
The `$constraint_value` parameter is optional on all functions; if this is not passed, the `default_constraint_value` from the config file will be used.

#### Set
```php
setting($userId)->set('key', 'value', $constraint_value);
```
Use `set` to change the value of a setting. If the setting does not exist, it will be created automatically. You can set multiple keys at once by passing an associative (key=>value) array to the first parameter.

#### Get
```php
setting($userId)->get('key', 'default', $constraint_value);
```
Use `get` to retrieve the value of a setting. The second parameter is optional and can be used to specify a default value if the setting does not exist (the default default value is `null`).

#### Forget
```php
setting($userId)->forget('key', $constraint_value);
```
Unset or delete a setting by calling `forget`.

#### Has
```php
setting($userId)->has('key', $constraint_value);
```
Check for the existence of a setting, returned as a boolean.

#### All
```php
setting($userId)->all($constraint_value);
```
Retrieve all settings as an associative array (key=>value).

#### Save
```php
setting($userId)->save($constraint_value);
```
Save all changes back to the database. This will need to be called after making changes; it is not automatic.

#### Load
```php
setting($userId)->load($constraint_value);
```
Reload settings from the database. This is called automatically if settings have not been loaded before being accessed or mutated.

#### call chaining
The functions below return the object of setting so you can invoke other functions.
`set` `forget` `save`
like this:

```php
setting($userId)->set('key', 'value', constraint_value)->get('key', 'default');
```

## Example
These examples are using the default configuration.

#### Using the default constraint value
The following sets and returns the currently logged in user's setting "example".
```php
// Set 'example' setting to 'hello world' and save to db
setting($userId)->set('example', 'hello world')->save();

// or use like:
$setting = setting($userId);
$setting->set('example', 'hello world')
$setting->save();

// Get the same setting
return setting($userId)->get('example');
```

## Finally

#### Contributing
Feel free to create a fork and submit a pull request if you would like to contribute.

#### Bug reports
Raise an issue on GitHub if you notice something broken.