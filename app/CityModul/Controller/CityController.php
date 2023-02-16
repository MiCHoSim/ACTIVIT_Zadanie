<?php

namespace App\CityModul\Controller;

use App\BaseModul\System\Controller\Controller;
use App\CityModul\Model\CityTable;
use Micho\Exception\ValidationException;
use Micho\Form\Form;
use Micho\Form\Input;
use PDOException;

/**
 * Class CityController
 */
class CityController extends Controller
{
    /**
     * @var CityTable Instance
     */
    private  CityTable $cityTable;

    public function __construct()
    {
        $this->cityTable = new CityTable();
    }

    /**
     ** Display and editing City
     * @return void
     * @Action
     */
    public function administration() : void
    {
        $this->createCityForm();

        $this->data['cities'] = $this->cityTable->get();

        $this->data['menu'] = [
            ['class' => '', 'title' => 'Weather forecast', 'href' => 'weather'],
            ['class' => 'active', 'title' => 'City Administration', 'href' => 'city/administration']
        ];

        $this->data['subview'] = '../app/CityModul/View/City/city';
        $this->view = '../vendor/Micho/layout';
    }

    /**
     ** Deleting a record in the database
     * @param string $cityId id of the city
     * @return void
     * @Action
     */
    public function delete(string $cityId) : void
    {
        try
        {
            $this->cityTable->delete($cityId);
        }
        catch (PDOException $error)
        {
            $this->addMessage('An error occurred while deleting a record from the database.', self::MSG_ERROR);
        }
        RouterController::$subPageControllerArray['description'] = 'Deleting city records.';
        $this->redirect('city/administration');
    }

    /**
     ** Create City Form
     * @return void
     * @throws \ErrorException
     */
    private function createCityForm() : void
    {
        $form = new Form('city');
        $form->addInput('City name', CityTable::NAME, Input::TYPE_TEXT,'','city');
        $form->addInput('Latitude', CityTable::LATITUDE, Input::TYPE_TEXT,'','city','','',true,array('description'=> '','pattern' =>'[+-]?([0-9]*[.])?[0-9]+'));
        $form->addInput('Longitude', CityTable::LONGITUDE, Input::TYPE_TEXT,'','city','','',true,array('description'=> '','pattern' =>'[+-]?([0-9]*[.])?[0-9]+'));
        $form->addSubmit('Add','save-button','city', 'btn btn-success btn-block font-weight-bolder');

        if($form->dataProcesing())
        {
            $formData = array();
            try
            {
                $formData = $form->getData();
                $form->validate($formData);
                $this->cityTable->save($formData);
                $this->addMessage('The city has been saved in the database.', self::MSG_SUCCESS);
                $this->redirect();
            }
            catch (ValidationException $error)
            {
                $form->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
            catch (PDOException $error)
            {
                $this->addMessage('The city is already stored in the database.', self::MSG_ERROR);
                $this->redirect();
            }
        }
        $this->data['form'] = $form->createForm();
    }
}
/*
 * Autor: MiCHo
 */