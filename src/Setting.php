<?php
namespace Lysice\HyperfUserSettings;

use Hyperf\DbConnection\Db;

class Setting {
    protected $table = '';

    protected $column = '';

    protected $custom_constraint = '';

    /**
     * table index used to specify the constraint
     * @var string
     */
    protected $constraint_key = '';

    /**
     * The default constraint value (used with the $constraint_key to generate a where clause).
     * This will only be used if $constraint_value is not specified.
     * Configured by the developer (see config/config.php for default).
     *
     * @var string
     */
    protected $default_constraint_value = '';


    /**
     * The settings cache.
     *
     * @var array
     */
    protected $settings = array();

    /**
     * Whether any settings have been modified since being loaded.
     * We use an array so different constraints can be flagged as dirty separately.
     *
     * @var bool
     */
    protected $dirty = array();

    /**
     * Whether settings have been loaded from the database (this session).
     * We use an array so different constraints can be loaded separately.
     *
     * @var array
     */
    protected $loaded = array();

    /**
     * Construction method to read package configuration.
     * @param $userId
     * @return void
     */
    public function __construct($userId = 0)
    {
        $this->table = config('user-setting.table');
        $this->column = config('user-setting.column');
        $this->custom_constraint = config('user-setting.custom_constraint');
        $this->constraint_key = config('user-setting.constraint_key');
        $this->default_constraint_value = config('user-setting.default_constraint_value');

        if(is_null($this->default_constraint_value)) {
            $this->default_constraint_value = $userId;
        }
    }

    /**
     * Get the value of a specific setting.
     *
     * @param string $key
     * @param mixed $default
     * @param string $constraint_value
     * @return mixed
     */
    public function get($key, $default = null, $constraint_value = null)
    {
        $constraint_value = $this->getConstraintValue($constraint_value);
        $this->check($constraint_value);

        return array_get($this->settings[$constraint_value], $key, $default);
    }

    /**
     * Set the value of a specific setting.
     *
     * @param string $key
     * @param mixed $value
     * @param string $constraint_value
     * @return Setting
     */
    public function set($key, $value = null, $constraint_value = null)
    {
        $constraint_value = $this->getConstraintValue($constraint_value);
        $this->check($constraint_value);

        $this->dirty[$constraint_value] = true;

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                array_set($this->settings[$constraint_value], $k, $v);
            }
        } else {
            array_set($this->settings[$constraint_value], $key, $value);
        }
        return $this;
    }

    /**
     * Unset a specific setting.
     *
     * @param string $key
     * @param string $constraint_value
     * @return Setting
     */
    public function forget($key, $constraint_value = null)
    {
        $constraint_value = $this->getConstraintValue($constraint_value);
        $this->check($constraint_value);

        if (array_key_exists($key, $this->settings[$constraint_value])) {
            unset($this->settings[$constraint_value][$key]);

            $this->dirty[$constraint_value] = true;
        }

        return $this;
    }

    /**
     * Check for the existence of a specific setting.
     *
     * @param string $key
     * @param string $constraint_value
     * @return bool
     */
    public function has($key, $constraint_value = null)
    {
        $constraint_value = $this->getConstraintValue($constraint_value);
        $this->check($constraint_value);

        if (!array_key_exists($constraint_value, $this->settings)) {
            return false;
        }

        return array_key_exists($key, $this->settings[$constraint_value]);
    }

    /**
     * Return the entire settings array.
     *
     * @param string $constraint_value
     * @return array
     */
    public function all($constraint_value = null)
    {
        $constraint_value = $this->getConstraintValue($constraint_value);
        $this->check($constraint_value);

        return $this->settings[$constraint_value];
    }

    /**
     * Save all changes back to the database.
     *
     * @param string $constraint_value
     * @return Setting
     */
    public function save($constraint_value = null)
    {
        $constraint_value = $this->getConstraintValue($constraint_value);
        $this->check($constraint_value);

        if ($this->dirty[$constraint_value]) {
            $json = json_encode($this->settings[$constraint_value]);

            $update = array();
            $update[$this->column] = $json;

            $constraint_query = $this->getConstraintQuery($constraint_value);

            Db::table($this->table)
                ->whereRaw($constraint_query)
                ->update($update);

            $this->dirty[$constraint_value] = false;
        }

        $this->loaded[$constraint_value] = true;
        return $this;
    }

    /**
     * Load settings from the database.
     *
     * @param string $constraint_value
     * @return void
     */
    public function load($constraint_value = null)
    {
        $constraint_value = $this->getConstraintValue($constraint_value);
        $constraint_query = $this->getConstraintQuery($constraint_value);
        $json = Db::table($this->table)
            ->whereRaw($constraint_query)
            ->value($this->column);

        $this->settings[$constraint_value] = empty($json) ? [] : json_decode($json, true);

        $this->dirty[$constraint_value] = false;
        $this->loaded[$constraint_value] = true;
    }

    /**
     * Check if settings have been loaded, load if not.
     *
     * @param string $constraint_value
     * @return void
     */
    protected function check($constraint_value)
    {
        if (empty($this->loaded[$constraint_value])) {
            $this->load($constraint_value);
            $this->loaded[$constraint_value] = true;
        }
    }

    /**
     * Get constraint value; use custom if specified or default.
     *
     * @param string $constraint_value
     * @return mixed
     */
    protected function getConstraintValue($constraint_value)
    {
        return $constraint_value ?: $this->default_constraint_value;
    }

    /**
     * Get constraint query.
     *
     * @param string $constraint_value
     * @return mixed
     */
    protected function getConstraintQuery($constraint_value)
    {
        return $this->custom_constraint ?: $this->constraint_key . ' = ' . $constraint_value;
    }

}