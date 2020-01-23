<?php
/**
 * @since: 22/Jan/2020
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @property:id,parent_id,user_id,title,points,is_done,deps,created_at,updated_at
 */
namespace App\models;

use App\core\AppController;
use App\core\AppModel;
use App\libraries\RemoteUserList;

class Mtasks extends AppModel
{
   
    public $id;
    public $parent_id;
    public $user_id;
    public $title;
    public $points;
    public $is_done;
    public $deps;
    public $created_at;
    public $updated_at;


    public function __construct()
    {
        parent::__construct();
        $this->setValidation();
        $this->tableName="tasks";
        $this->primaryKey="id";
        $this->uniqueKey=array();
        $this->multiKey=array();
        $this->autoIncField=array("id");
    }

    public function isValidForm($isNew = true, $addError = true, $isSelectOnly = false)
    {
        $isOk=false;

        if (parent::isValidForm($isNew, $addError, $isSelectOnly)) {
            $isOk=true;
            if ($isNew || $this->isSetPrperty("user_id")) {
                if (!RemoteUserList::isExistsUser($this->user_id)) {
                    $isOk=false;
                    AppController::addError("user_id doesn't exists");
                }
            }
            if ($isNew || $this->isSetPrperty("is_done")) {
                if ($this->is_done !=0 && $this->is_done!=1) {
                    $isOk=false;
                    AppController::addError("The is_done field must be one of: 0,1");
                }
            }
            //check parent id
            if ($isNew || $this->isSetPrperty("parent_id")) {
                if (! empty($this->parent_id)) {
                    //check parent id
                    $checkObject = new self();
                    $checkObject->id($this->parent_id);
                    if (! $checkObject->select()) {
                        $isOk = false;
                        AppController::addError("parent_id doesn't exists");
                    } else {
                        $nextDeps=$checkObject->deps+1;
                        if ($nextDeps>4) {
                            $isOk = false;
                            AppController::addError("maximum child depth is 5");
                        } else {
                            $this->deps($nextDeps);
                        }
                    }
                }
            }
        }

        return $isOk;
    }
    public function setValidation()
    {
        $this->validations=array(
            "id"=>array("Text"=>"Id", "Rule"=>"max_length[10]|integer"),
            "parent_id"=>array("Text"=>"parent_id", "Rule"=>"max_length[11]|integer|differs[id]"),
            "user_id"=>array("Text"=>"user_id", "Rule"=>"required|max_length[11]|integer"),
            "title"=>array("Text"=>"title", "Rule"=>"required|max_length[255]"),
            "points"=>array("Text"=>"points", "Rule"=>"required|max_length[2]|integer|greater_than_equal_to[1]|less_than_equal_to[10]"),
            "is_done"=>array("Text"=>"is_done", "Rule"=>"required|integer|max_length[1]"),
            "deps"=>array("Text"=>"Deps", "Rule"=>"max_length[1]"),
            "created_at"=>array("Text"=>"Created At", "Rule"=>"max_length[20]"),
            "updated_at"=>array("Text"=>"Updated At", "Rule"=>"max_length[20]")
            
        );
    }
    public function castPropertyTypes(&$setProperties)
    {
        parent::castPropertyTypes($setProperties);
        if (empty($this->parent_id)) {
            $this->parent_id=null;
        }
    }
    public function update($notLimit = false, $isShowMsg = true, $dontProcessIdWhereNotSet = true)
    {
        if (! $this->isSetPrperty("updated_at")) {
            $this->updated_at(date('Y-m-d H:i:s'));
        }
        $isNeedToUpdateChild=false;
        $isNeedToUpdateParent=false;
        if ($this->isSetPrperty("is_done")) {
            $isNeedToUpdateChild= (int)$this->is_done===1;
            $isNeedToUpdateParent=(int)$this->is_done===0;
        }
        $current_id= $this->getWhereProperty("id");
        if (parent::update($notLimit, $isShowMsg, $dontProcessIdWhereNotSet)) {
            if ($isNeedToUpdateChild) {
                $childObj= new self();
                $childObj->parent_id($current_id);
                $childs=$childObj->selectAll();

                foreach ($childs as $child) {
                    $childUpdate=new self();
                    $childUpdate->is_done($this->is_done);
                    $childUpdate->setWhereCondition("id", $child->id);
                    $childUpdate->update();
                }
            }
            if ($isNeedToUpdateParent) {
                $currentObject= new self();
                $currentObject->id($current_id);
                if ($currentObject->select() && !empty($currentObject->parent_id)) {
                  //update parent
                    $parentUpdate=new self();
                    $parentUpdate->is_done(0);
                    $parentUpdate->setWhereCondition("id", $currentObject->parent_id);
                    $parentUpdate->update();
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function save()
    {
        if (! $this->isSetPrperty("created_at")) {
            $this->created_at(date('Y-m-d H:i:s'));
        }
        if (! $this->isSetPrperty("updated_at")) {
            $this->updated_at($this->created_at);
        }
        $parent_id=null;
        if ($this->isSetPrperty("parent_id")) {
            if ($this->isSetPrperty("is_done") && (int)$this->is_done===0) {
                $parent_id = $this->parent_id;
            }
        }
        if (parent::save()) {
            if (!empty($parent_id)) {
                $parentUpdate = new self();
                $parentUpdate->is_done(0);
                $parentUpdate->setWhereCondition("id", $parent_id);
                $parentUpdate->update();
            }
            return true;
        }
        return false;
    }

    public static function getStat($user_id, $id = "")
    {
        $obj=new self();
        if (empty($id)) {
            $query="SELECT count(*) as totalItem, sum(points) as totalPoint,sum(CASE WHEN  is_done = 1 THEN points ELSE 0 END) as completed
                                        FROM tasks where  user_id={$user_id}";

        } else {
            $query="SELECT count(*) as totalItem, sum(points) as totalPoint,sum(CASE WHEN  is_done = 1 THEN points ELSE 0 END) as completed
                                        FROM tasks where  user_id={$user_id} AND parent_id={$user_id}";
        }
        $results = $obj->selectQuery($query);
        $response=new \stdClass();
        $response->totalItem= 0 ;
        $response->totalPoint= 0;
        $response->completed=0;
        if (isset($results[0]) && $results[0]->totalItem>0) {
            $response->totalItem= $results[0]->totalItem;
            $response->totalPoint= $results[0]->totalPoint;
            $response->completed=$results[0]->completed;
        } elseif (!empty($id)) {
            $obj=new self();
            $obj->id($id);
            if ($obj->select()) {
                $response->totalItem=1;
                $response->totalPoint=$obj->points;
                $response->totalPoint=$obj->is_done==1?$obj->points:0;
            }
        }
        return $response;
    }
}
