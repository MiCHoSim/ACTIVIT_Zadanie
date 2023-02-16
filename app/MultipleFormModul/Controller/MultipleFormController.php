<?php

namespace App\MultipleFormModul\Controller;

use App\BaseModul\System\Controller\Controller;
use App\BaseModul\System\Controller\RouterController;
use App\MultipleFormModul\Model\UserDataTable;
use Micho\Exception\ValidationException;
use Micho\Form\Form;
use PDOException;

/**
 * Class MultipleFormController
 */
class MultipleFormController extends Controller
{
    /**
     * @var UserDataTable Instance
     */
    private  UserDataTable $userDataTable;

    public function __construct()
    {
        $this->userDataTable = new UserDataTable();
    }

    /**
     ** save user data
     * @return void
     * @Action
     */
    public function save($step = 1, $userDataId = null) : void
    {
        $this->createForm($step, $userDataId);

        $this->data['cities'] = $this->userDataTable->get();

        $this->data['menu'] = [
            ['class' => 'active', 'title' => 'User data Form', 'href' => 'multiple-form/save'],
            ['class' => '', 'title' => 'List of users', 'href' => 'multiple-form/users-data']
        ];

        $this->data['subview'] = '../app/MultipleFormModul/View/multipleForm/save-data';
        $this->view = '../vendor/Micho/layout';
    }

    /**
     ** Display and editing userData
     * @return void
     * @Action
     */
    public function usersData() : void
    {
        $this->data['usersData'] = $this->userDataTable->get();

        $this->data['menu'] = [
            ['class' => '', 'title' => 'User data Form', 'href' => 'multiple-form/save'],
            ['class' => 'active', 'title' => 'List of users', 'href' => 'multiple-form/users-data']
        ];
        $this->data['subview'] = '../app/MultipleFormModul/View/multipleForm/users-data';
        $this->view = '../vendor/Micho/layout';
    }

    /**
     ** Deleting a record in the database
     * @param string $userId id user data
     * @return void
     * @Action
     */
    public function delete(string $userId) : void
    {
        try
        {
            $this->userDataTable->delete($userId);
        }
        catch (PDOException $error)
        {
            $this->addMessage('An error occurred while deleting a record from the database.', self::MSG_ERROR);
        }
        RouterController::$subPageControllerArray['description'] = 'Deleting city records.';
        $this->redirect('multiple-form/users-data');
    }

    /**
     ** Create User mutlti-step Form
     * @return void
     * @throws \ErrorException
     */
    private function createForm(int $step, $userDataId) : void
    {
        $userDataTable = new UserDataTable((int)$userDataId);

        $formData = $userDataTable->getArrayData();

        $buttonName = 'Next';
        $next = '/' . $step + 1;
        if($step === 1)        //create Person detail form
        {
            $messaage = 'Person detail';
            $form = new Form('user-data', UserDataTable::PERSON);
        }
        elseif ($step === 2 && $userDataId)        //create Adress Form
        {
            $messaage = 'Adress';
            $form = new Form('user-data', UserDataTable::ADDRESS);
        }
        elseif ($step === 3 && $userDataId)        //create Bank Form
        {
            $messaage = 'Bank';
            $buttonName = 'Save';
            $next = '';
            $form = new Form('user-data', UserDataTable::BANK);
        }
        else
            $this->redirect('multiple-form/save');

        $form->addSubmit($buttonName,'save-button','user-data', 'btn btn-success btn-block font-weight-bolder');
        $form->setValuesControls($formData);
        if($form->dataProcesing())
        {
            try
            {
                $formData = $form->getData();
                $form->validate($formData);

                $formData[UserDataTable::ID] = $userDataId;

                $userDataIdDb = $this->userDataTable->save($formData);

                $userDataId = $userDataIdDb ? $userDataIdDb : $userDataId; // pri Update nevracia Id

                $this->addMessage( $messaage . ' data has been saved.', self::MSG_SUCCESS);

                $this->redirect('multiple-form/save' . $next . '/' . $userDataId);
            }
            catch (ValidationException $error)
            {
                $form->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
        }

        $this->data['form'] = $form->createForm();
        if($step > 1)
            $this->data['form']['previous-button'] = '<a class="btn btn-secondary btn-block font-weight-bolder" href="multiple-form/save/'  . $step-1 . '/' . $userDataId . '">Previous</a>';
    }
}
/*
 * Autor: MiCHo
 */