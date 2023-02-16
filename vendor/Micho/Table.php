<?php

namespace Micho;

use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Abstract  class Table
 */
abstract class Table
{
    /**
     * @var array array all attributes
     */
    protected array $arrayData;

    /**
     * @var Child class
     */
    private $thisChild;

    /**
     * @param bool|int $id Primmary key
     */
    public function __construct(bool|int $id = false)
    {
        $this->thisChild = get_class($this);
        if ($id)
        {
            $data =  Db::queryOneRow('SELECT * FROM '. $this->thisChild::TABLE . ' 
                                            WHERE ' . $this->thisChild::ID . ' = ?', array($id));

            $data = is_array($data) ? ArrayUtilities::filterKeys($data, $this->keys) : array();

            $this->setAtributes($data);
        }
        $this->setArrayData();
    }

    /**
     ** Save the read data to the Database
     * @param array|bool $data Data to save
     * @return string
     */
    public function save(array|bool $data = false) : string
    {
        $data = $data ? $data : $this->arrayData;

        $data = ArrayUtilities::filterKeys($data, $this->keys);

        return Db::insert($this->thisChild::TABLE, $data);
    }

    /**
     ** Loads data from the Database
     * @return mixed
     */
    public function get() : mixed
    {
        $data = Db::queryAllRows('SELECT ' . implode(', ', $this->keys) .
            ' FROM ' . $this->thisChild::TABLE);

        return is_array($data) ? ArrayUtilities::filterKeys($data,$this->keys) : $data;
    }

    /**
     ** automatic saving of values in variable attributes
     * @param array $data Data to save
     * @return void
     */
    public function setAtributes(array $data)
    {
        foreach ($data as $key => $dat)
        {
            $nameAttribute = StringUtilities::underlineToCamel($key);
            $this->$nameAttribute = $dat; //ulozi hodnotu atributu
        }
        $this->setArrayData();
    }

    /**
     ** Insert data into the array
     * @return void
     */
    protected function setArrayData(): void
    {
        foreach ($this->keys as $key)
        {
            $nameAttribute = StringUtilities::underlineToCamel($key);
            $this->arrayData[$key] = $this->$nameAttribute;
        }
    }
    /**
     ** Deleting a record in the database
     * @param string $id id row table
     * @return void
     */
    public function delete(string $id) : void
    {
        Db::query('DELETE FROM ' . $this->thisChild::TABLE . ' WHERE ' . $this->thisChild::ID . ' = ? ', array($id));
    }

    /**
     ** Getter for Keys of table
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     ** Getter for attribute in array
     * @return array
     */
    public function getArrayData(): array
    {
        return $this->arrayData;
    }
}