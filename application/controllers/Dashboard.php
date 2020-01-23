<?php
namespace app\controllers;

use App\core\AppController;
use App\models\Mtasks;
use GuzzleHttp\Client;

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AppController
{


    /**
     * it is the main function to load home page
     */
    public function index()
    {

        $this->display();
    }
}
