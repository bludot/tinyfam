<?php

namespace Core;
use Core\Inflection;
use Core\App;
use Medoo\Medoo as Medoo;

abstract class Model implements \ArrayAccess, \IteratorAggregate, \JsonSerializable {
    /**
     * @var medoo
     */
    private static $_medoo;
    /**
     * @var array
     */
    protected $_properties = [];
    /**
     * @var array
     */
    protected $_modified = [];
    /**
     * @var array
     */
    protected static $_meta = [];
    /**
     * @var array
     */
    protected static $_validate = [];
    /**
     * @var array
     */
    protected $_exported = [];
    /**
     * @var bool
     */
    protected $_trace = true;
    /**
     * @var bool
     */
    protected $_new = true;
    /**
     * @param string $name
     * @return Model
     */
    public static function getInstance($name) {
    $name = Inflection::singularize($name);
        $className = "{$name}Model";
        $result = App::loadLibrary($className);
        if($result == true) {
            $model = new $className();
            return $model;
        } else {
            return false;
        }
    }
    /**
     * @param medoo $medoo
     */
    public static function setMedoo(Medoo $medoo) {
        self::$_medoo = $medoo;
    }
    public function __construct($trace_modify = true) {
        $this->_trace = $trace_modify;
    }
    public function setup($template) {
        $this->_trace = false;
        foreach ($template as $name => $value) {
            $this->prop_set($name, $value);
        }
        $this->type_correct();
        $this->_trace = true;
        $this->_new = false;
        return $this;
    }
    private function type_correct() {
        foreach (static::$_meta as $name => $type) {
            if (is_null($this->_properties[$name]))
                continue;
            switch ($type) {
                case 'str':
                case 'string':
                    $this->_properties[$name] = strval($this->_properties[$name]);
                    break;
                case 'int':
                case 'integer':
                    $this->_properties[$name] = intval($this->_properties[$name]);
                    break;
                case 'flt':
                case 'dbl':
                case 'real':
                case 'float':
                case 'double':
                    $this->_properties[$name] = floatval($this->_properties[$name]);
                    break;
                case 'bool':
                case 'boolean':
                    $this->_properties[$name] = boolval($this->_properties[$name]);
                    break;
            }
        }
    }
    public function load($id, $clone=false) {
        if ($clone) {
            $r = $this->medoo->fetch_class(get_class($this), [false])
                ->get($this->get_table_name(), '*', ['id' => $id]);
            if ($r instanceof Model) {
                $r->type_correct();
                $r->_trace = true;
                $r->_new = false;
                return $r;
            }
        } else {
            $r = $this->medoo->get($this->get_table_name(), '*', ['id' => $id]);
            if (!empty($r)) {
                $this->setup($r);
                return $this;
            }
        }
        return null;
    }
    public function one($where = null, $clone = false) {
        if ($clone) {
            $r = $this->medoo->fetch_class(get_class($this), [false])
                ->get($this->get_table_name(), '*', $where);
            if ($r instanceof Model) {
                $r->type_correct();
                $r->_trace = true;
                $r->_new = false;
                return $r;
            }
        } else {
            $r = $this->medoo->get($this->get_table_name(), '*', $where);
            if (!empty($r)) {
                $this->setup($r);
                return $this;
            }
        }
        return null;
    }
    public function many($where = null) {
        $r = $this->medoo->fetch_class(get_class($this), [false])
            ->select($this->get_table_name(), '*', $where);
        if (!empty($r)) {
            /** @var Model $o */
            foreach ($r as &$o) {
                $o->type_correct();
                $o->_trace = true;
                $o->_new = false;
            }
            return $r;
        }
        return [];
    }
    public function count($where = null) {
        return $this->medoo->count($this->get_table_name(), '*', $where);
    }
    public function sql($sql) {
        /** @var PDOStatement $stmt */
        $stmt = $this->medoo->query($sql);
        $r = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this), [$this->db, false]);
        if (!empty($r)) {
            /** @var Model $o */
            foreach ($r as &$o) {
                $o->type_correct();
                $o->_trace = true;
                $o->_new = false;
            }
            return $r;
        }
        return [];
    }
    public function save() {
        if ($this->_new) {
            $id = $this->medoo->insert($this->get_table_name(), $this->_properties);
            if (empty($this->_properties['id']))
                $this->_properties['id'] = $id;
        } else {
            if (empty($this->_modified))
                return $this;
            $this->medoo->update($this->get_table_name(), $this->_modified, [
                'id' => $this->_properties['id']
            ]);
        }
        $this->_modified = [];
        $this->_new = false;
        return $this;
    }
    public function remove($id = null) {
        if (empty($id)) {
            $id = $this->id;
        }
        $this->medoo->delete($this->get_table_name(), ['id' => $id]);
        return $this;
    }
    public function get_table_name() {
        return substr( // remove the Model suffix
            basename( // remove the namespaces
                strtolower(get_class($this)) // use the lower case class name as table name
            ), 0, -5
        );
    }
    public function get_properties() {
        return $this->_properties;
    }
    /**
     * @return medoo
     */
    private function get_medoo() {
        return self::$_medoo;
    }
    public function prop_set($name, $value) {
        if ($this->_trace
            && array_key_exists($name, $this->_properties)
            && $this->_properties[$name] !== $value
        ) {
            $this->_modified[$name] = $value;
        }
        $this->_properties[$name] = $value;
        return $this;
    }
    protected function export() {
        $exported = array_keys($this->_properties);
        $plus = [];
        $diff = [];
        foreach ($this->_exported as $act) {
            if ($act[0] == '+') {
                $plus[] = trim(substr($act, 1));
            } elseif ($act[0] == '-') {
                $diff[] = trim(substr($act, 1));
            }
        }
        return array_diff(array_merge($exported, $plus), $diff);
    }
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        $getter = "get_$name";
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (array_key_exists($name, $this->_properties)) {
            return $this->_properties[$name];
        } else {
            return null;
        }
    }
    public function __set($name, $value) {
        $setter = "set_$name";
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (array_key_exists($name, $this->_properties)) {
            $this->prop_set($name, $value);
        }
    }
    public function __isset($name) {
        $getter = "get_$name";
        return
            method_exists($this, $getter) ||
            array_key_exists($name, $this->_properties) ||
            isset($this->$name);
    }
    public function __sleep() {
        return ['_properties', '_modified', '_new', '_trace', '_exported'];
    }
    public function __toString() {
        return $this->table_name . '_' . $this->id;
    }

    /// ArrayAccess methods
    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        return $this->__isset($offset);
    }
    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) {
        return $this->__get($offset);
    }
    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) {
        $this->__set($offset, $value);
    }
    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        // Nothing to do
    }
    /// IteratorAggregate methods
    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() {
        $export = $this->export();
        foreach ($export as $name) {
            yield $name => $this->$name;
        }
    }
    /// JsonSerializable methods
    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize() {
        $result = [];
        foreach ($this->getIterator() as $name => $value) {
            $result[$name] = $value;
        }
        return $result;
    }
    public static function validate(&$data, $validates=null) {
        $defaultSetting = [
            'type' => 'string',
            'default' => null,
            'canBeNull' => true, // if false, null value would be convert automatically
            'hash' => false,
            'trim' => true, // trim before checking
            'emptyCheck' => 'empty', // callable or false to disable empty check
            'onEmpty' => 'set',// set/return define onEmptyReturn if use return
            'onEmptySet' => '__DEFAULT__', //set as default value
            // an array if enable error check
            // first element can be 'match', 'contain', 'is', 'not', 'eq', 'ne', 'lt', 'gt', 'el', 'eg', 'call'
            // second element should be:
            //     regular expression for match
            //     comparable value for is/not/eq/ne/lt/gt/el/eg
            //     callable for call, pass current checking value , entire $data and current checking $key, expecting bool return.
            'errorCheck' => false,
            // if errorCheck is enabled, onError can be set or return
            // onErrorSet and onErrorReturn would be use.
        ];
        if (empty($validates))
            $validates = static::$_validate;
        foreach ($validates as $name => $vDef) {
            if ($name == '__DEFAULT_RETURN__') continue;
            if (!array_key_exists($name, $data)) continue;
            $validate = array_merge($defaultSetting, $vDef);
            $value = &$data[$name];
            if (is_string($value) && $validate['trim']) {
                $value = trim($value);
            }
            if (!is_null($value) || !$validate['canBeNull']) {
                switch ($validate['type']) {
                    case 'str':
                    case 'string':
                        $value = strval($value);
                        break;
                    case 'int':
                    case 'integer':
                        $value = intval($value);
                        break;
                    case 'flt':
                    case 'dbl':
                    case 'real':
                    case 'float':
                    case 'double':
                        $value = floatval($value);
                        break;
                    case 'bool':
                    case 'boolean':
                        $value = boolval($value);
                        break;
                }
            }
            if ($validate['emptyCheck'] === 'empty') {
                $isEmpty = empty($value);
            } elseif (is_callable($validate['emptyCheck'])) {
                $isEmpty = call_user_func($validate['emptyCheck'], $value);
            } else {
                $isEmpty = false;
            }
            if ($isEmpty) {
                $onEmpty  = strtolower($validate['onEmpty']);
                if ($onEmpty === 'set') {
                    $value = $validate['onEmptySet'] === '__DEFAULT__' ?
                        $validate['default'] : $validate['onEmptySet'];
                    continue; //skip error check
                } elseif ($onEmpty === 'return') {
                    return $validate['onEmptyReturn'];
                } else {
                    throw new Exception('Unknown onEmpty action for validating ' . $name);
                }
            }
            if (is_array($validate['errorCheck'])) {
                list($operation, $param) = $validate['errorCheck'];
                switch ($operation) {
                    case 'match':
                        $noError = preg_match($param, $value) ? true : false;
                        break;
                    case 'contain':
                        $noError = strpos($value, $param) !== false;
                        break;
                    case 'call':
                        if (is_callable($param))
                            $noError = call_user_func($param, $value, $data, $name) !== false;
                        else
                            throw new Exception('Error check function for ' . $name . ' is not callable');
                        break;
                    case 'is':
                    case '===':
                        $noError = $value === $param;
                        break;
                    case 'not':
                    case '!==':
                        $noError = $value !== $param;
                        break;
                    case 'eq':
                    case '==':
                        $noError = $value == $param;
                        break;
                    case 'ne':
                    case '!=':
                        $noError = $value != $param;
                        break;
                    case 'between':
                    case '<>':
                        $min = min($param);
                        $max = max($param);
                        $noError = $value >= $min && $value <= $max;
                        break;
                    case 'gt':
                    case '>':
                        $noError = $value > $param;
                        break;
                    case 'lt':
                    case '<':
                        $noError = $value < $param;
                        break;
                    case 'eg':
                    case '>=':
                        $noError = $value >= $param;
                        break;
                    case 'el':
                    case '<=':
                        $noError = $value <= $param;
                        break;
                    default:
                        throw new Exception("Unknown check operation for $name");
                }
            } else {
                $noError = true;
            }
            if (!$noError) {
                $onError = strtolower($validate['onError']);
                if ($onError === 'set') {
                    $value = $validate['onErrorSet'] === '__DEFAULT__' ?
                        $validate['default'] : $validate['onErrorSet'];
                } elseif ($onError === 'return') {
                    return $validate['onErrorReturn'];
                }
            }
        }
        return $validates['__DEFAULT_RETURN__'] ?? true;
    }
    public function set_exported(array $exp) {
        $this->_exported = $exp;
    }
    public function get_exported():array {
        return $this->_exported;
    }
}