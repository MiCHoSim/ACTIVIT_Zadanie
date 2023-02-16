<?php

namespace App\MultipleFormModul\Model;

use Micho\Form\Form;
use Micho\Table;
use Micho\Utilities\ArrayUtilities;
use Micho\Db;
use Micho\Exception\ValidationException;
use PDOException;

/**
 * Class for UserDataTable
 */
class UserDataTable extends Table
{
    /**
     * Name of the Table
     */
    const TABLE = 'user_data';

    /**
     * Database constants
     */
    const ID = 'user_data_id';
    const NAME = Form::NAME;
    const LAST_NAME = Form::LAST_NAME;
    const STREET = Form::STREET;
    const REGISTER_NUMBER = Form::REGISTER_NUMBER;
    const POSTCODE = Form::POSTCODE;
    const CITY = Form::CITY;
    const IBAN = Form::IBAN;

    /**
     * @var null Attributes
     */
    protected int|null $userDataId = null;
    protected string|null $name = null;
    protected string|null $lastName = null;
    protected string|null $street= null;
    protected string|null $registerNumber = null;
    protected string|null $postCode = null;
    protected string|null $city = null;
    protected string|null $iban = null;

    /**
     * Cont for form
     */
    CONST PERSON = [self::NAME,self::LAST_NAME];
    CONST ADDRESS = [self::STREET,self::REGISTER_NUMBER,self::POSTCODE,self::CITY];
    CONST BANK = [self::IBAN];

    /**
     * @var array Database keys
     */
    protected array $keys;

    public function __construct(bool|int $id = false)
    {
        $this->keys = array_merge(array(self::ID), self::PERSON,self::ADDRESS,self::BANK);
        parent::__construct($id);
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

        try
        {
            $data = Db::insert(self::TABLE, $data,true);
        }
        catch (PDOException $error)
        {
            throw new ValidationException('Validation errors occurred', 0, null,
                array('The data is already stored in the database.'));
        }
        return $data;
    }
}
/*
 * Autor: MiCHo
 */