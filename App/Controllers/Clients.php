<?php

namespace App\Controllers;

use App\Models\Client;
use App\View;

class Clients
{
    private $model;
    private $view;

    public function __construct()
    {

        $this->view = new View();

        try {
            $this->model = new Client();$cols = $this->model->showColumns('clients');
            $this->view->set(compact('cols'));
        } catch (\Exception $e) {
            self::error($e->getMessage());
        }
    }

    public function error($message = '')
    {
        $this->view->set([ 'message' => $message ]);
        $this->view->display('error');
    }

    public function index()
    {
        $q = htmlspecialchars_decode($_GET['q']);
        $clients = $this->model->getList($q);

        $this->view->set(compact('q', 'clients'));
        $this->view->display('index');
    }

    public function view($id = 0)
    {
        $client = $this->model->getOne($id);

        $this->view->set(compact('id', 'client'));
        $this->view->display('view');
    }

    public function create()
    {
        if (!empty($_POST)) {
            $this->model->create($_POST);
        } else {
            $this->view->display('create');
        }
    }

    public function edit($id = 0)
    {
        if (!empty($_POST)) {
            $this->model->edit($_POST);
        } else {
            $client = $this->model->getOne($id);

            $this->view->set(compact('id', 'client'));
            $this->view->display('edit');
        }
    }

    public function delete($id)
    {
        $this->model->delete($id);
    }

}