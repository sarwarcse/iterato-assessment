<?php
/**
 * @since: 23/01/2020
 * @author: Sarwar Hasan
 * @version 1.0.0
 */
namespace App\controllers\api;

use App\core\RestController;
use App\libraries\RemoteUserList;
use App\models\Mtasks;

class Task extends RestController
{
    public function __construct()
    {
        parent::__construct();
        if (!RemoteUserList::loadUsers()) {
            $this->setInternalError();
        }
    }

    /**
     * this method is for check user with email.
     * @param $id
     * @param $email
     *
     * @return bool
     */
    protected function validUserIdWithEmail($id, $email)
    {
        if (RemoteUserList::isExistsUser($id, $email)) {
            return true;
        }
        self::addError("user_id doesn't match with the email address");
        return false;
    }


    /**
     * this method is for put or update task
     */
    public function put()
    {
        $id= $this->uri->segment(3);
        if (empty($id)) {
            self::addError("task ID is required to update");
            $this->errorResponse(self::HTTP_BAD_REQUEST);
            return;
        }

        $params=$this->getParams();
        $this->filterArray($params);
        //now check user exists or not
        $oldTask=Mtasks::findBy("id", $id);
        if (!empty($oldTask)) {
            $mTask = new Mtasks();
            if ($mTask->setFromArray($params)) {
                $isOkUser = true;
                //Now i want to check email is email is match with user_id or not, if it doesn't required to check then this line can be avoid
                //the bellows checking is commented, in the sample input documentation it saw it has been valid for miss match email too.
                /*if (isset($params['email'])) {
                  $isOkUser=$this->validUserIdWithEmail($mTask->user_id, $params['email']);
                }*/


                if ($isOkUser) {
                    $mTask->setWhereCondition("id", $id);
                    if ($mTask->update()) {
                        $mainObj = new Mtasks();
                        $mainObj->id($id);
                        if ($mainObj->select()) {
                            unset($mainObj->deps);
                            $this->response($mainObj, self::HTTP_CREATED);
                        } else {
                            $this->errorResponse(self::HTTP_BAD_REQUEST);
                        }

                        return;
                    }
                }
            } else {
                $this->errorResponse(self::HTTP_BAD_REQUEST);
            }
            //$data= $this->input->input_stream();
        } else {
            self::addError("task is doesn't exists by this ID");
            $this->errorResponse(self::HTTP_BAD_REQUEST);
        }
    }

    /**
     * it is for task input
     */
    public function post()
    {
        $params=$_POST;
        $this->filterArray($params);
        //now check user exists or not
        $mTask=new Mtasks();
        if ($mTask->setFromArray($params)) {
            $isOkUser=true;
            //Now i want to check email is email is match with user_id or not, if it doesn't required to check then this line can be avoid
            //the bellows checking is commented, in the sample input documentation it saw it has been valid for miss match email too.
            /*if (isset($params['email'])) {
                $isOkUser=$this->validUserIdWithEmail($mTask->user_id, $params['email']);
            }*/

            if ($isOkUser) {
                if ($mTask->save()) {
                    unset($mTask->deps);
                    $this->response($mTask, self::HTTP_CREATED);
                    return;
                }
            }
        }
        $this->errorResponse(self::HTTP_BAD_REQUEST);

        //$data= $this->input->input_stream();
    }
}
