<?php

namespace App\CityModul\Model;

use Micho\Table;

/**
 * Class for table City
 */
class CityTable extends Table
{
    /**
     * Name of the Table
     */
    const TABLE = 'city';

    /**
     * Database constants
     */
    const ID = 'city_id';
    const NAME = 'name';
    const LATITUDE = 'latitude';
    const LONGITUDE = 'longitude';

    /**
     * @var array Database keys
     */
    protected $keys = [self::ID,self::NAME,self::LATITUDE,self::LONGITUDE];

    /**
     * @var null Attributes
     */
    protected int|null $cityId = null;
    protected string|null $name = null;
    protected float|null $latitude = null;
    protected float|null $longitude = null;
}
/*
 * Autor: MiCHo
 */