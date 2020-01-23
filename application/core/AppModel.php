<?php
/**
 * @author Sarwar
 * @ Last Updated: 23/OCT/2016
 */

namespace App\core;

use App\core\AppController;

class AppModel extends \CI_Model
{
    protected $validations;
    protected $setProperties;
    protected $likesFields;
    protected $setOption;
    protected $updateWhereExtraField;
    protected $updateWhereExtraFieldOption=array();
    protected $tableName;
    protected $tableShortForm="";
    protected $autoIncField;
    protected $primaryKey;
    protected $uniqueKey;
    protected $multiKey;
    protected $mySqlError;
    protected $settedPropertyforLog = "";
    protected $htmlInputField = array ();
    protected $isWhereSet=false;
    protected $isValidationRule=false;
    protected $appName="";
    protected $checkCache=false;
    protected $cacheTime=300; //5 minitue
    protected $textDomain="";
    /**
     * @var ObjectJoin[]
     */
    protected $joinObjects=array();
    /**
     * @var CI_DB_query_builder
     */
    private static $db1;
    /**
     * @var CI_DB_query_builder
     */
    private static $db2;
    private $avoidCustomCheck=false;
    private $group_by=null;
    public function __construct()
    {
        $this->appName                     =$this->config->item('app_name');
        $this->tableShortForm              ="";
        $this->setProperties               = array ();
        $this->setOption                   = array ();
        $this->updateWhereExtraField       = array ();
        $this->updateWhereExtraFieldOption =array();
        $this->uniqueKey                   = array ();
        $this->multiKey                    = array ();
        $this->autoIncField                =array();
        $this->likesFields                 =array();
    }
    protected function getPropertyRawOptions($property, $isWithSelect = false)
    {
        if ($isWithSelect) {
            return array(""=>"Select");
        }
        return array();
    }
    public function getPropertyOptions($property, $isWithSelect = false)
    {
        $returnobj = $this->getPropertyRawOptions($property, $isWithSelect);
        foreach ($returnobj as &$v) {
            $v = __($v);
        }
        return $returnobj;
    }
    public function getPropertyOptionsColor($property)
    {
        
        return array();
    }
    public function getPropertyOptionsIcon($property)
    {
    
        return array();
    }
    public function getPropertyOptionsTag(
        $property,
        $tag = 'span',
        $class_prefix = 'text-',
        $class_postfix = '',
        $default = ''
    ) {
        $properties=$this->getPropertyOptions($property);
        if (count($properties)>0) {
            $colors=$this->getPropertyOptionsColor($property);
            $icons=$this->getPropertyOptionsIcon($property);
            foreach ($properties as $key => &$value) {
                $color=!empty($colors[$key])?$colors[$key]:$default;
                $icon=!empty($icons[$key])?'<i class="'.$icons[$key].'"></i>':"";
                $value="<{$tag} class=\"{$class_prefix}{$color}{$class_postfix}\">{$icon} {$value}</{$tag}>";
            }
        }
        return $properties;
    }
    public function checkCache($setValue = true, $cacheTime = 0)
    {
        $is_cache=$this->config->item("custom_cache");
        if ($is_cache) {
            $this->checkCache=$setValue;
            if ($cacheTime>0) {
                $this->cacheTime=60*$cacheTime;
            }
        }
    }
    public function settedPropertyforLog()
    {
        return $this->settedPropertyforLog;
    }
    public function setTableShortName($name)
    {
        $this->tableShortForm=$name;
    }
    /**
     * check the table name and othething
     * @return boolean
     */
    protected function checkBasicCheck()
    {
        if (empty($this->tableName)) {
            return false;
        }
        return true;
    }

    public function getPostValue($name, $default = "", $isXsClean = true)
    {
        $objdata=$this->$name;
        if (!empty($this->$name) || ( is_string($objdata)&& $objdata."_-A"==="0_-A")) {
            $default = $this->$name;
        }
        $postvalue=$this->input->post($name, $isXsClean);
        $this->doFieldValueFilter($name, $postvalue, $isXsClean);
        return !empty($postvalue)?$postvalue:$default;
    }
    
    public function addGroupBy($key)
    {
        $this->group_by=$key;
    }
    
    protected function setCustomParamData()
    {
        if (count($this->setOption)==0) {
            return;
        }
        $tbname= $this->getTableName().".";
        foreach ($this->setOption as $key => $value) {
            $this->$key=str_replace('[fld]', $tbname.$key, $this->$key);
            $this->setProperties[$key]=str_replace('[fld]', $tbname.$key, $this->setProperties[$key]);
        }
    }
    protected function onSaveUpdateEvent()
    {
    }
    
    /**
     * It user for specific for setting, It could be based on session.
     * @param array $alreadyadded
     * @param CI_DB_query_builder $db
     */
    protected function setCustomModelWhereProperties()
    {
        return;
    }
    public function avoidCustomModelWhereProperties($isAvoid = true)
    {
        $this->avoidCustomCheck=$isAvoid;
    }
    /**
     * @param unknown $extraParam
     * @param string $isSelectDb
     * @return boolean
     */
    protected function setDBSelectWhereProperties($extraParam = array(), $clear_properties = true, $isSelectDb = true)
    {
        if (!$this->avoidCustomCheck) {
            $this->setCustomModelWhereProperties();
        }
        $alreadyadded=array();
        $tbname= $this->getTableName().".";
        $this->setCustomParamData();
        if ($isSelectDb) {
            $db=$this->getSelectDB();
        } else {
            $db=$this->getUpdateDB();
        }
        if (empty($this->tableName)) {
            return false;
        }
        
        //primary key query
        $primaryKey=$this->primaryKey;
        if (!empty($primaryKey) && isset($this->setProperties[$primaryKey])) {
            if (!empty($this->setOption [$primaryKey])) {
                $db->where("(".$tbname.$primaryKey." ".$this->$primaryKey.")", "", false);
            } else {
                $db->where($tbname.$primaryKey, $this->$primaryKey);
            }
            $alreadyadded[]=$primaryKey;
            $this->isWhereSet=true;
        }
        $generalKeys=array();
        // Unique Index key query
        if (count($this->uniqueKey)>0) {
            if (is_array($this->uniqueKey[0])) {
                $selectedKey="";
                foreach ($this->uniqueKey as $pos => $uk) {
                    $generalKeys=array_merge($generalKeys, $uk);
                    $isOk=true;
                    foreach ($uk as $fld) {
                        if (!isset($this->setProperties[$fld])) {
                            $isOk=false;
                            break;
                        }
                    }
                    if ($isOk) {
                         $selectedKey=$pos;
                    }
                }
                if ($selectedKey!="") {
                    foreach ($this->uniqueKey[$selectedKey] as $uk) {
                        if (!in_array($uk, $alreadyadded) && isset($this->setProperties[$uk])) {
                            if (!empty($this->setOption [$uk])) {
                                $db->where("(".$tbname.$uk." ".$this->$uk.")", "", false);
                            } else {
                                $db->where($tbname.$uk, $this->$uk);
                            }
                            $alreadyadded[]=$uk;
                            $this->isWhereSet=true;
                        }
                    }
                }
            } else {
                //for backword compatibility
                foreach ($this->uniqueKey as $uk) {
                    if (!in_array($uk, $alreadyadded) && isset($this->setProperties[$uk])) {
                        if (!empty($this->setOption [$uk])) {
                            $db->where("(".$tbname.$uk." ".$this->$uk.")", "", false);
                        } else {
                            $db->where($tbname.$uk, $this->$uk);
                        }
                        $alreadyadded[]=$uk;
                        $this->isWhereSet=true;
                    }
                }
            }
        }
        
        // Other's key query
        if (count($this->multiKey)>0) {
            if (is_array($this->multiKey[0])) {
                $selectedKey="";
                foreach ($this->multiKey as $pos => $uk) {
                    $generalKeys=array_merge($generalKeys, $uk);
                    $isOk=true;
                    foreach ($uk as $fld) {
                        if (!isset($this->setProperties[$fld])) {
                            $isOk=false;
                            break;
                        }
                    }
                    if ($isOk) {
                        $selectedKey=$pos;
                    }
                }
                if ($selectedKey!="") {
                    foreach ($this->multiKey[$selectedKey] as $uk) {
                        if (!in_array($uk, $alreadyadded) && isset($this->setProperties[$uk])) {
                            if (!empty($this->setOption [$uk])) {
                                $db->where("(".$tbname.$uk.$this->setProperties[$uk].")", "", false);
                            } else {
                                $db->where($tbname.$uk, $this->setProperties[$uk]);
                            }
                            $alreadyadded[]=$uk;
                            $this->isWhereSet=true;
                        }
                    }
                }
            } else {
                //for backword compatibility
                foreach ($this->multiKey as $uk) {
                    if (!in_array($uk, $alreadyadded) && isset($this->setProperties[$uk])) {
                        if (!empty($this->setOption [$uk])) {
                            $db->where("(".$tbname.$uk.$this->setProperties[$uk].")", "", false);
                        } else {
                            $db->where($tbname.$uk, $this->setProperties[$uk]);
                        }
                        $alreadyadded[]=$uk;
                        $this->isWhereSet=true;
                    }
                }
            }
        }
        
        //for GeneralKeys
        foreach ($generalKeys as $uk) {
            if (!in_array($uk, $alreadyadded)) {
                if (isset($this->setProperties[$uk])) {
                    if (!empty($this->setOption [$uk])) {
                        $db->where("(".$tbname.$uk." ".$this->$uk.")", "", false);
                    } else {
                        $db->where($tbname.$uk, $this->$uk);
                    }
                    $alreadyadded[]=$uk;
                    $this->isWhereSet=true;
                }
            }
        }
        
        foreach ($this->setProperties as $key => $value) {
            if (property_exists($this, $key) && !in_array($key, $alreadyadded)) {
                if (!empty($this->setOption [$key])) {
                    $db->where("(".$tbname.$key." ".$this->$key.")", "", false);
                } else {
                    $db->where($tbname.$key, $this->$key);
                }
                $alreadyadded[]=$key;
            }
        }
        foreach ($extraParam as $key => $value) {
            if (property_exists($this, $key) && !in_array($key, $alreadyadded)) {
                if (!empty($this->setOption [$key])) {
                    $db->where("(".$tbname.$key." ".$value.")", "", false);
                } else {
                    $db->where($tbname.$key, $value);
                }
                $alreadyadded[]=$key;
            }
        }
        //like properties
        if (count($this->likesFields)>0) {
            foreach ($this->likesFields as $likefld) {
                $db->like($likefld->field, $likefld->value, $likefld->likeside);
            }
        }
        if (!empty($this->group_by)) {
            $db->group_by($this->group_by);
        }
        if ($clear_properties) {
            $this->resetSetForInsetUpdate();
        }
        
        return true;
    }
    
    /**
     * @param CI_DB_query_builder $dbobj
     * @param unknown $extraParam
     * @param string $clear_properties
     * @return boolean
     */
    public function setDBSelectJoinProperties($db, $extraParam = array(), $clear_properties = true)
    {
        $alreadyadded=array();
        $tbname= $this->getTableName().".";
        
        if (empty($this->tableName)) {
            return false;
        }
    
        //primary key query
        $primaryKey=$this->primaryKey;
        if (!empty($primaryKey) && isset($this->setProperties[$primaryKey])) {
            if (!empty($this->setOption [$primaryKey])) {
                $db->where("(".$tbname.$primaryKey." ".$this->$primaryKey.")", "", false);
            } else {
                $db->where($tbname.$primaryKey, $this->$primaryKey);
            }
            $alreadyadded[]=$primaryKey;
            $this->isWhereSet=true;
        }
    
        
        
        
        
        
        
        
        $generalKeys=array();
        // Unique Index key query
        if (count($this->uniqueKey)>0) {
            if (is_array($this->uniqueKey[0])) {
                $selectedKey="";
                foreach ($this->uniqueKey as $pos => $uk) {
                    $generalKeys=array_merge($generalKeys, $uk);
                    $isOk=true;
                    foreach ($uk as $fld) {
                        if (!isset($this->setProperties[$fld])) {
                            $isOk=false;
                            break;
                        }
                    }
                    if ($isOk) {
                        $selectedKey=$pos;
                    }
                }
                if ($selectedKey!="") {
                    foreach ($this->uniqueKey[$selectedKey] as $uk) {
                        if (!in_array($uk, $alreadyadded) && isset($this->setProperties[$uk])) {
                            if (!empty($this->setOption [$uk])) {
                                $db->where("(".$tbname.$uk." ".$this->$uk, "", false);
                            } else {
                                $db->where($tbname.$uk, $this->$uk);
                            }
                            $alreadyadded[]=$uk;
                            $this->isWhereSet=true;
                        }
                    }
                }
            } else {
                foreach ($this->uniqueKey as $uk) {
                    if (!in_array($uk, $alreadyadded) && isset($this->setProperties[$uk])) {
                        if (!empty($this->setOption [$uk])) {
                            $db->where("(".$tbname.$uk." ".$this->$uk, "", false);
                        } else {
                            $db->where($tbname.$uk, $this->$uk);
                        }
                        $alreadyadded[]=$uk;
                        $this->isWhereSet=true;
                    }
                }
            }
        }
        
        // Other's key query
        if (count($this->multiKey)>0) {
            if (is_array($this->multiKey[0])) {
                $selectedKey="";
                foreach ($this->multiKey as $pos => $uk) {
                    $generalKeys=array_merge($generalKeys, $uk);
                    $isOk=true;
                    foreach ($uk as $fld) {
                        if (!isset($this->setProperties[$fld])) {
                            $isOk=false;
                            break;
                        }
                    }
                    if ($isOk) {
                        $selectedKey=$pos;
                    }
                }
                if ($selectedKey!="") {
                    foreach ($this->multiKey[$selectedKey] as $uk) {
                        if (!in_array($uk, $alreadyadded) && isset($this->setProperties[$uk])) {
                            if (!empty($this->setOption [$uk])) {
                                $db->where("(".$tbname.$uk." ".$this->$uk.")", "", false);
                            } else {
                                $db->where($tbname.$uk, $this->$uk);
                            }
                            $alreadyadded[]=$uk;
                            $this->isWhereSet=true;
                        }
                    }
                }
            } else {
                //for backword compatibility
                foreach ($this->multiKey as $uk) {
                    if (!in_array($uk, $alreadyadded) && isset($this->setProperties[$uk])) {
                        if (!empty($this->setOption [$uk])) {
                            $db->where("(".$tbname.$uk." ".$this->$uk.")", "", false);
                        } else {
                            $db->where($tbname.$uk, $this->$uk);
                        }
                        $alreadyadded[]=$uk;
                        $this->isWhereSet=true;
                    }
                }
            }
        }
        
        //for GeneralKeys
        foreach ($generalKeys as $uk) {
            if (!in_array($uk, $alreadyadded)) {
                if (isset($this->setProperties[$uk])) {
                    if (!empty($this->setOption [$uk])) {
                        $db->where("(".$tbname.$uk." ".$this->$uk.")", "", false);
                    } else {
                        $db->where($tbname.$uk, $this->$uk);
                    }
                    $alreadyadded[]=$uk;
                    $this->isWhereSet=true;
                }
            }
        }
        
        foreach ($this->setProperties as $key => $value) {
            if (property_exists($this, $key) && !in_array($key, $alreadyadded)) {
                if (!empty($this->setOption [$key])) {
                    $db->where("(".$tbname.$key." ".$this->$key.")", "", false);
                } else {
                    $db->where($tbname.$key, $this->$key);
                }
                $alreadyadded[]=$key;
            }
        }
        foreach ($extraParam as $key => $value) {
            if (property_exists($this, $key) && !in_array($key, $alreadyadded)) {
                if (!empty($this->setOption [$key])) {
                    $db->where("(".$tbname.$key." ".$value.")", "", false);
                } else {
                    $db->where($tbname.$key, $value);
                }
                $alreadyadded[]=$key;
            }
        }
        if ($clear_properties) {
            $this->setProperties=array();
            $this->setOption=array();
        }
    
        return true;
    }
    
    public function join($join_obj, $join_obj_property, $main_obj_property, $type = "", $as = "", $extraParam = [])
    {
        if (!empty($as)) {
            $join_obj->setTableShortName($as);
        }
        $joinobj=new ObjectJoin();
        $joinobj->join_obj=$join_obj;
        $joinobj->join_obj_property=$join_obj_property;
        $joinobj->main_obj_property=$main_obj_property;
        $joinobj->type=$type;
        $joinobj->extra_param=$extraParam;
        $this->joinObjects[]=$joinobj;
    }
    
    protected function setJoinProperties($clear_properties = true)
    {
        if (count($this->joinObjects)>0) {
            foreach ($this->joinObjects as $jn) {
                //$jn=new ObjectJoin();
                $thistblstrproperty=$this->getTableNameForJoinProperty($jn->main_obj_property);
                if (property_exists($jn->join_obj, $jn->join_obj_property) && !empty($thistblstrproperty)) {
                    $tablestr=$jn->join_obj->GetTableName(false);
                    $shorttbl=$jn->join_obj->GetTableName();
                    $extra_param_str="";
                    if (count($jn->extra_param)>0) {
                        foreach ($jn->extra_param as $fd => $vd) {
                            $extra_param_str.=!empty($extra_param_str)?" AND ":"";
                            $extra_param_str.=$fd."=".$vd;
                        }
                        $extra_param_str=" AND $extra_param_str";
                    }
                    $this->getSelectDB()->join($tablestr, " $shorttbl.$jn->join_obj_property=$thistblstrproperty $extra_param_str", $jn->type);
                    $jn->join_obj->SetDBSelectJoinProperties($this->getSelectDB(), array(), $clear_properties);
                }
            }
        }
    }
    private function getTableNameForJoinProperty($propertyName)
    {
        if (strpos($propertyName, ".") !== false) {
            return $propertyName;
        }
        if (property_exists($this, $propertyName)) {
            return $this->getTableName().".$propertyName";
        } else {
            if (count($this->joinObjects)>0) {
                foreach ($this->joinObjects as $jn) {
                    if (property_exists($jn->join_obj, $propertyName)) {
                        return $jn->join_obj->GetTableName().".$propertyName";
                    }
                }
            }
        }
        return "";
    }
    
    protected function setDBUpdateWhereProperties($extraParam = array(), $isCheckWherePropetrySetOrNot = true, $clear_properties = false)
    {
        if (!$this->checkBasicCheck()) {
            return false;
        }
        if (count($this->updateWhereExtraField)==0) {
            return false;
        }
        $alreadyadded=array();
        //primary key query
        $primaryKey=$this->primaryKey;
        if (!empty($primaryKey) && isset($this->updateWhereExtraField[$primaryKey])) {
            if (in_array($primaryKey, $this->updateWhereExtraFieldOption)) {
                $this->getUpdateDB()->where("(".$primaryKey.$this->updateWhereExtraField[$primaryKey].")", "", false);
            } else {
                $this->getUpdateDB()->where($primaryKey, $this->updateWhereExtraField[$primaryKey]);
            }
            $alreadyadded[]=$primaryKey;
        }
    
        
        $generalKeys=array();
        // Unique Index key query
        if (count($this->uniqueKey)>0) {
            if (is_array($this->uniqueKey[0])) {
                $selectedKey="";
                foreach ($this->uniqueKey as $pos => $uk) {
                    $generalKeys=array_merge($generalKeys, $uk);
                    $isOk=true;
                    foreach ($uk as $fld) {
                        if (!isset($this->updateWhereExtraField[$fld])) {
                            $isOk=false;
                            break;
                        }
                    }
                    if ($isOk) {
                        $selectedKey=$pos;
                    }
                }
                if ($selectedKey!="") {
                    foreach ($this->uniqueKey[$selectedKey] as $uk) {
                        if (isset($this->updateWhereExtraField[$uk]) && !in_array($uk, $alreadyadded)) {
                            if (in_array($uk, $this->updateWhereExtraFieldOption)) {
                                $this->getUpdateDB()->where("(".$uk.$this->updateWhereExtraField[$uk].")", "", false);
                            } else {
                                $this->getUpdateDB()->where($uk, $this->updateWhereExtraField[$uk]);
                            }
                            $alreadyadded[]=$uk;
                        }
                    }
                }
            } else {
                //for backword compatibility
                // Other's key query
                foreach ($this->uniqueKey as $uk) {
                    if (isset($this->updateWhereExtraField[$uk]) && !in_array($uk, $alreadyadded)) {
                        if (in_array($uk, $this->updateWhereExtraFieldOption)) {
                            $this->getUpdateDB()->where("(".$uk.$this->updateWhereExtraField[$uk].")", "", false);
                        } else {
                            $this->getUpdateDB()->where($uk, $this->updateWhereExtraField[$uk]);
                        }
                        $alreadyadded[]=$uk;
                    }
                }
            }
        }
        
        // Other's Multikey query
        
        // Other's key query
        if (count($this->multiKey)>0) {
            if (is_array($this->multiKey[0])) {
                $selectedKey="";
                foreach ($this->multiKey as $pos => $uk) {
                    $generalKeys=array_merge($generalKeys, $uk);
                    $isOk=true;
                    foreach ($uk as $fld) {
                        if (!isset($this->updateWhereExtraField[$fld])) {
                            $isOk=false;
                            break;
                        }
                    }
                    if ($isOk) {
                        $selectedKey=$pos;
                    }
                }
                if ($selectedKey!="") {
                    foreach ($this->multiKey[$selectedKey] as $uk) {
                        if (isset($this->updateWhereExtraField[$uk])&& !in_array($uk, $alreadyadded)) {
                            if (in_array($uk, $this->updateWhereExtraFieldOption)) {
                                $this->getUpdateDB()->where("(".$uk.$this->updateWhereExtraField[$uk].")", "", false);
                            } else {
                                $this->getUpdateDB()->where($uk, $this->updateWhereExtraField[$uk]);
                            }
                            $alreadyadded[]=$uk;
                        }
                    }
                }
            } else {
                //for backword compatibility
                foreach ($this->multiKey as $uk) {
                    if (isset($this->updateWhereExtraField[$uk])&& !in_array($uk, $alreadyadded)) {
                        if (in_array($uk, $this->updateWhereExtraFieldOption)) {
                            $this->getUpdateDB()->where("(".$uk.$this->updateWhereExtraField[$uk].")", "", false);
                        } else {
                            $this->getUpdateDB()->where($uk, $this->updateWhereExtraField[$uk]);
                        }
                        $alreadyadded[]=$uk;
                    }
                }
            }
        }
        //for GeneralKeys
        foreach ($generalKeys as $uk) {
            if (!in_array($uk, $alreadyadded)) {
                if (isset($this->updateWhereExtraField[$uk])) {
                    if (in_array($uk, $this->updateWhereExtraFieldOption)) {
                        $this->getUpdateDB()->where("(".$uk.$this->updateWhereExtraField[$uk].")", "", false);
                    } else {
                        $this->getUpdateDB()->where($uk, $this->updateWhereExtraField[$uk]);
                    }
                    $alreadyadded[]=$uk;
                    $this->isWhereSet=true;
                }
            }
        }
        
            
        foreach ($this->updateWhereExtraField as $key => $value) {
            if (property_exists($this, $key) && !in_array($key, $alreadyadded)) {
                if (in_array($key, $this->updateWhereExtraFieldOption)) {
                    $this->getUpdateDB()->where("(".$key.$this->updateWhereExtraField[$key].")", "", false);
                } else {
                    $this->getUpdateDB()->where($key, $this->updateWhereExtraField[$key]);
                }
                $alreadyadded[]=$key;
            }
        }
            
        foreach ($extraParam as $key => $value) {
            if (property_exists($this, $key) && !in_array($key, $alreadyadded)) {
                $this->getUpdateDB()->where($key, $value);
                $alreadyadded[]=$key;
            }
        }
        if ($isCheckWherePropetrySetOrNot) {
            if (count($alreadyadded)==0) {
                add_model_errors_code("E004");
                return false;
            }
        }
        if ($clear_properties) {
            $this->updateWhereExtraField=array();
            $this->updateWhereExtraFieldOption=array();
        }
        return true;
    }
    public function unsetAllUpdateProperty()
    {
        $this->updateWhereExtraField=array();
        $this->updateWhereExtraFieldOption=array();
    }
    public function setDBPropertyForInsertOrUpdate($isForUpdate = false)
    {
        if (!$this->checkBasicCheck()) {
            return false;
        }
        if (!$isForUpdate) {
            $primaryKey = $this->primaryKey;
            if (!empty($primaryKey) && !isset($this->setProperties[$primaryKey]) && !in_array($primaryKey, $this->autoIncField)) {
                add_model_errors_code("E002");
                return false;
            }
        }
        $primaryKey = $this->primaryKey;
        foreach ($this->setProperties as $key => $value) {
            if ($isForUpdate && $primaryKey==$key) {
                continue;
            }
            if (!empty($this->setOption [$key])) {
                $this->getUpdateDB()->set($key, $this->$key." ", false);
            } else {
                $this->getUpdateDB()->set($key, $this->$key);
            }
        }
        $this->resetSetForInsetUpdate();
        return true;
    }
    public function addLike($likefld, $likeValue, $likeside = "both")
    {
        if (property_exists($this, $likefld)) {
            $std=new stdClass();
            $std->field=$likefld;
            $std->value=$likeValue;
            $std->likeside=$likeside;
            $this->likesFields[]=$std;
        }
    }
    /**
     * @param string $likefld
     * @param string $likeValue
     * @param string $likeside
     * @param bool $isSelectDb
     */
    public function setDBLike($likefld, $likeValue, $likeside = "after", $isSelectDb = true)
    {
        $db=$isSelectDb?$this->getSelectDB():$this->getUpdateDB();
        //set like
        if (! empty($likefld)) {
            if (property_exists($this, $likefld)) {
                if (count($this->joinObjects)>0) {
                    $likefld= $this->getTableName().".".$likefld;
                }
                $db->like($likefld, $likeValue, $likeside);
            } else {
                if (count($this->joinObjects)>0) {
                    foreach ($this->joinObjects as $jn) {
                //$jn=new ObjectJoin();
                        $thistblstrproperty=$this->getTableNameForJoinProperty($likefld);
                        if (property_exists($jn->join_obj, $likefld) && !empty($thistblstrproperty)) {
                            $likefld=$thistblstrproperty;
                            $db->like($likefld, $likeValue, $likeside);
                            break;
                        }
                    }
                }
            }
        }
    }
    /**
     * @param string|array $order_by
     * @param string $order
     * @param bool $isSelectDb
     */
    public function setDBOrder($order_by, $order = "", $isSelectDb = true, $isEscap = true)
    {
        $db=$isSelectDb?$this->getSelectDB():$this->getUpdateDB();
        //SetOrder
        if (is_array($order_by)) {
            $forder="";
            foreach ($order_by as $op => $ov) {
                $forder.="$op $ov ,";
            }
            $forder=rtrim($forder, ',');
            $db->order_by($forder);
        } elseif (! empty($order_by) && property_exists($this, $order_by)) {
            $db->order_by($order_by, $order);
        } elseif (!empty($order_by) && property_exists($this, $order_by) && empty($order)) {
            $db->order_by($order_by);
        } elseif (!$isEscap) {
            $db->order_by($order_by);
        }
    }
    /**
     * @param number $limit
     * @param number $limitStart
     * @param bool $isSelectDb
     */
    public function setDBLimit($limit, $limitStart = 0, $isSelectDb = true)
    {
        $db=$isSelectDb?$this->getSelectDB():$this->getUpdateDB();
        $db->limit($limit, $limitStart);
    }
    /**
     * @param string $select
     * @param bool $isSelectDb
     */
    public function setDBSelect($select = "", $isSelectDb = true, $isEscap = true)
    {
        $db=$isSelectDb?$this->getSelectDB():$this->getUpdateDB();
        $dbname= $this->getTableName();
        if (empty($select)) {
            $select =$dbname . ".* ";
        } else {
            $select=explode(",", $select);
            foreach ($select as $key => &$se) {
                $se=trim($se);
                if (strpos($se, ".") !== false) {
                    continue;
                }
                if ($se=="*") {
                    $se=$dbname . ".* ";
                } elseif (property_exists($this, $se)) {
                    $se="$dbname.$se ";
                } elseif (!$isEscap) {
                    continue;
                } else {
                    if (count($this->joinObjects)>0) {
                        foreach ($this->joinObjects as $jn) {
                            if (property_exists($jn->join_obj, $se)) {
                                $se=$jn->join_obj->GetTableName().".$se";
                            }
                        }
                    } else {
                        unset($select[$key]);
                    }
                }
            }
            $select=implode(", ", $select);
        }
        $db->select($select);
    }
    
    
    
    /**
     * @param bool $isOnlyTableName
     * @return string
     */
    public function getTableName($isOnlyTableName = true)
    {
        if (!empty($this->tableShortForm)) {
            if ($isOnlyTableName) {
                return $this->tableShortForm;
            } else {
                return $this->tableName." as ".$this->tableShortForm;
            }
        }
        return $this->tableName;
    }
    
    protected function bindObject($obj)
    {
        if (!empty($obj) && (is_object($obj) || is_array($obj))) {
            foreach ($obj as $key => $value) {
                if (in_array($key, $this->htmlInputField)) {
                    $value = stripcslashes($value);
                }
                $this->$key = $value;
            }
        }
        $this->castPropertyTypes($this);
    }

    protected function setCustomValidationMessage()
    {
        //$this->form_validation;
    }
    protected function setValidationRule($isForNew = true)
    {
        $this->form_validation->reset_validation();
        $this->form_validation->set_data($this->setProperties);
        
        if ($isForNew) {
            foreach ($this->validations as $key => $value) {
                if (!empty($this->setOption[$key])) {
                    continue;
                }
                if (!empty($value['Rule'])) {
                    $this->isValidationRule = true;
                    $this->form_validation->set_rules($key, $value['Text'], $value['Rule']);
                }
            }
        } else {
            if (count($this->setProperties) > 0) {
                foreach ($this->setProperties as $key => $value) {
                    if (!empty($this->setOption[$key])) {
                        continue;
                    }
                    if (isset($this->validations [$key]) && $this->validations [$key] ['Rule'] != "") {
                        $this->isValidationRule = true;
                        $this->form_validation->set_rules($key, $this->validations [$key] ['Text'], $this->validations [$key] ['Rule']);
                    } else {
                        //$this->form_validation->set_rules ( $key, '', '' );
                    }
                }
            }
        }
        $this->setCustomValidationMessage($this->form_validation);
    }
    public function isValidForm($isNew = true, $addError = true, $isSelectOnly = false)
    {
        $this->setValidationRule($isNew);
        if (!$this->isValidationRule) {
            return true;
        }
        if ($isSelectOnly) {
            $this->form_validation->dontChangePostValue=true;
        }
        if ($this->form_validation->run() == false) {
            if ($addError) {
                $this->addValidationErrors();
            }
            return  false;
        } else {
            return true;
        }
    }
    /**
     * Validation Error String
     *
     * Returns all the errors associated with a form submission. This is a helper
     * function for the form validation class.
     *
     */
    protected function addValidationErrors()
    {
        if (false === ($OBJ =& _get_validation_object())) {
            return '';
        }

        $errors=$OBJ->error_array();
        foreach ($errors as $val) {
            if ($val !== '') {
                AppController::addError($val);
            }
        }
    }
    public function save()
    {
        if (! $this->isValidForm(true)) {
            return false;
        }
        if (!$this->setDBPropertyForInsertOrUpdate()) {
            return false;
        }
        if ($this->getUpdateDB()->insert($this->tableName)) {
            if (is_array($this->autoIncField) && count($this->autoIncField)>0) {
                $auto_inserted=$this->getUpdateDB()->insert_id();
                foreach ($this->autoIncField as $fld) {
                    if (property_exists($this, $fld)) {
                        $this->$fld=$auto_inserted;
                    }
                }
            }
            $this->resetSetForInsetUpdate();
            $this->onSaveUpdateEvent();
            return true;
        } else {
            add_model_errors_code("E003");
            return false;
        }
    }
    /**
     * @param string $select
     * @return boolean
     */
    public function selectArray($select = "", $addFieldError = false)
    {
        return $this->select($select, $addFieldError, false);
    }
    
    /**
     * @param string $select
     * @return boolean
     */
    public function select($select = "", $addFieldError = false, $isObject = true)
    {
        if (!$this->checkBasicCheck()) {
            return false;
        }
        if (!$this->isValidForm(false, $addFieldError, true)) {
            return false;
        }
        if ($this->checkCache) {
            $cacheid=md5("select".$this->tableName.json_encode($this->setProperties).json_encode($this->joinObjects).$select.$isObject);
            $response_data = get_cache_data($cacheid);
            if (!empty($response_data) || is_object($response_data) || is_array($response_data)) {
                $this->bindObject($response_data);
                return true;
            }
        }
        if (!$this->setDBSelectWhereProperties(array(), true, true)) {
            return false;
        }
        $this->setDBSelect($select, true);
        $this->setJoinProperties();
        $result = $this->getSelectDB()->get($this->getTableName(false));
        if ($result && $result->num_rows() >0) {
            if ($isObject) {
                $firstRow=$result->first_row();
                if ($this->checkCache) {
                    save_cache_data($cacheid, $firstRow, $this->cacheTime);
                }
                $this->bindObject($firstRow);
            } else {
                return $result->first_row('array');
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * @param string $select
     * @return NULL || self
     */
    public function selectCustom($select = "", $addFieldError = false, $isObject = true)
    {
        if (!$this->checkBasicCheck()) {
            return false;
        }
        if (!$this->isValidForm(false, $addFieldError, true)) {
            return false;
        }
        if ($this->checkCache) {
            $cacheid=md5("select".$this->tableName.json_encode($this->setProperties).json_encode($this->joinObjects).$select.$isObject);
            $response_data = get_cache_data($cacheid);
            if (!empty($response_data) || is_object($response_data) || is_array($response_data)) {
                $this->bindObject($response_data);
                return true;
            }
        }
        if (!$this->setDBSelectWhereProperties(array(), true, true)) {
            return false;
        }
        $this->getSelectDB()->select($select, false);
        $this->setJoinProperties();
        $result = $this->getSelectDB()->get($this->getTableName(false));
        if ($result && $result->num_rows() >0) {
            if ($isObject) {
                $firstRow=$result->first_row();
                if ($this->checkCache) {
                    save_cache_data($cacheid, $firstRow, $this->cacheTime);
                }
                return $firstRow;
            } else {
                return $result->first_row('array');
            }
        } else {
            return null;
        }
    }
    /**
     * @param string $select
     * @param string $likefld
     * @param string $likeValue
     * @param unknown $extraParam
     * @param string $likeside
     * @return boolean|NULL
     */
    public function customSelect($select = "*", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after")
    {
        $this->getSelectDB()->select("$select", false);
        if (empty($this->tableName)) {
            return false;
        }
        if (!$this->setDBSelectWhereProperties($extraParam, false, true)) {
            return false;
        }
        //set like
        $this->setDBLike($likefld, $likeValue, $likeside, true);
        //SetOrder
        $this->setJoinProperties();
    
        $result = $this->getSelectDB()->get($this->getTableName(false));
        if ($result && $result->num_rows() >0) {
            return $result->first_row();
        }
        return null;
    }
    /**
     *
     * @param string $select
     * @param string $orderBy
     * @param string $order
     * @param string $limit
     * @param string $limitStart
     * @param string $likefld
     * @param string $like
     * @param Array $ExtraLike
     * @return static []
     */
    public function selectAll($select = "", $orderBy = "", $order = "", $limit = "", $limitStart = "", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isEscap = true, $is_data_only = false)
    {
        if (!$this->checkBasicCheck()) {
            return array();
        }
        $isshowerror=ENVIRONMENT=="development";
        if ($this->checkCache) {
            $cache_id=$is_data_only?"selectall_data":"selectall";
            $cacheid=md5($cache_id.$this->tableName.json_encode($this->setProperties).json_encode($this->joinObjects).$likefld.$likeValue.$order.$orderBy.$limit.$limitStart.$likeside.json_encode($extraParam));
            $response_data = get_cache_data($cacheid);
            if (is_array($response_data)) {
                return $response_data;
            }
        }
        
        if (! $this->isValidForm(false, $isshowerror, true)) {
            return array();
        }
        if (!$this->setDBSelectWhereProperties($extraParam, true, true)) {
            return array();
        }
        
        $this->setDBLike($likefld, $likeValue, $likeside, true);
        
        //SetOrder
        $this->setDBOrder($orderBy, $order, true, $isEscap);
        if(!empty($limit)) {
            $this->setDBLimit($limit, $limitStart);
        }
        $this->setDBSelect($select, true, $isEscap);
        $this->setJoinProperties();
        
        $result = $this->getSelectDB()->get($this->getTableName(false));
        if ($result && $result->num_rows()>0) {
            if ($this->checkCache) {
                if ($is_data_only) {
                    $result_dara=$result->result();
                } else {
                    $result_dara=$result->result(get_class($this));
                }
                save_cache_data($cacheid, $result_dara, $this->cacheTime);
                return $result_dara;
            } else {
                if ($is_data_only) {
                    return $result_dara=$result->result();
                } else {
                    return $result_dara=$result->result(get_class($this));
                }
            }
        } else {
            if ($this->checkCache) {
                save_cache_data($cacheid, array(), $this->cacheTime);
            }
            return array();
        }
    }
    /**
     *
     * @param string $select
     * @param string $orderBy
     * @param string $order
     * @param string $limit
     * @param string $limitStart
     * @param string $likefld
     * @param string $like
     * @param Array $ExtraLike
     * @return static []
     */
    public function selectAllGridData($select = "", $orderBy = "", $order = "", $limit = "", $limitStart = "", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isEscap = true)
    {
        return $this->selectAll($select, $orderBy, $order, $limit, $limitStart, $likefld, $likeValue, $extraParam, $likeside, $isEscap, true);
    }
    /**
     * @param string $select
     * @param string $orderBy
     * @param string $order
     * @param string $limit
     * @param string $limitStart
     * @param string $likeFld
     * @param string $likeValue
     * @param unknown $extraParam
     * @param string $likeSide
     * @param string $isEscap
     * @param boolean $isCache
     * @param int $cacheTime
     *
     * @return static []
     */
    public static function fetchAll(
        $select = "",
        $orderBy = "",
        $order = "",
        $limit = "",
        $limitStart = "",
        $likeFld = "",
        $likeValue = "",
        $extraParam = array(),
        $likeSide = "after",
        $isEscap = true,
        $isCache = false,
        $cacheTime = 0
    ) {
        $s = new static();
        $s->checkCache($isCache, $cacheTime);

        return $s->selectAll(
            $select,
            $orderBy,
            $order,
            $limit,
            $limitStart,
            $likeFld,
            $likeValue,
            $extraParam,
            $likeSide,
            $isEscap
        );
    }
    /**
     * @param unknown $property
     * @param unknown $value
     * @return APP_Model|NULL|static
     */
    public static function findBy($property, $value, $extraParam = array(), $isCache = false, $cacheTime = 0)
    {
        $n = new static();
        $n->checkCache($isCache, $cacheTime);
        if (property_exists($n, $property)) {
            $n->$property($value);
            if (is_array($extraParam)) {
                foreach ($extraParam as $key => $value) {
                    if (property_exists($n, $key)) {
                        $n->$key($value);
                    }
                }
            }
            if ($n->select()) {
                return $n;
            }
        }

        return null;
    }
    
    /**
     * @param String $property
     * @param unknown $value
     * @param unknown $extraparam
     * @param string $isCache
     * @param number $cacheTime
     * @return static []:
     */
    static function findAllBy($property, $value, $extraparam = array(), $order_by = '', $order = 'ASC', $limit = "", $limitStart = "", $isCache = false, $cacheTime = 0)
    {
        $n =new static();
        $n->checkCache($isCache, $cacheTime);
        if (property_exists($n, $property)) {
            $n->$property($value);
            if (is_array($extraparam)) {
                foreach ($extraparam as $key => $value) {
                    if (property_exists($n, $key)) {
                        $n->$key($value);
                    }
                }
            }
            return $n->selectAll('', $order_by, $order, $limit, $limitStart);
        }
    
        return array();
    }
    
    /**
     * @param $findByProperty
     * @param $findByValue
     * @param $key
     * @param $value
     * @param array $extraParam
     * @param bool $isCache
     * @param int $cacheTime
     *
     * @return static []
     */
    public static function findAllByKeyValue($findByProperty, $findByValue, $key, $value, $extraParam = array(), $isCache = false, $cacheTime = 0)
    {
        $n =new static();
        $n->checkCache($isCache, $cacheTime);
        if (property_exists($n, $findByProperty)) {
            $n->$findByProperty($findByValue);
            return $n->selectAllWithKeyValue($key, $value, "", "", "", "", "", "", $extraParam);
        }
        
        return array();
    }
    
    /**
     * @param $findByProperty
     * @param $findByvalue
     * @param $identity_fld
     * @param array $extraparam
     * @param bool $isCache
     * @param int $cacheTime
     *
     * @return static []
     */
    public static function findAllByIdentity(
        $findByProperty,
        $findByvalue,
        $identity_fld,
        $extraparam = array(),
        $isCache = false,
        $cacheTime = 0
    ) {
        $n =new static();
        $n->checkCache($isCache, $cacheTime);
        if (property_exists($n, $findByProperty)) {
            $n->$findByProperty($findByvalue);
            return $n->selectAllWithIdentity($identity_fld, "", "", "", "", "", "", $extraparam);
        }
        
        return array();
    }
    public function getPropertiesArray($skipped = "")
    {
        $skipped=explode(",", $skipped);
        $return=array();
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $skipped[]="settedPropertyforLog";
        foreach ($properties as $property) {
            if (in_array($property->getName(), $skipped)) {
                continue;
            }
            $return[$property->getName()]=$property->getValue($this);
        }
        return $return;
    }
    public static function fetchCountAll($likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isCache = false, $cacheTime = 0)
    {
        $s=new static();
        $s->checkCache($isCache, $cacheTime);
        return $s->countAll($likefld, $likeValue, $extraParam, $likeside);
    }
    public static function fetchAllKeyValue($key, $value, $isStarAdd = false, $orderBy = "", $order = "", $limit = "", $limitStart = "", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isEscap = true, $isCache = false, $cacheTime = 0)
    {
        $s=new static();
        $s->checkCache($isCache, $cacheTime);
        $results=$s->selectAll($key.",".$value, $orderBy, $order, $limit, $limitStart, $likefld, $likeValue, $extraParam, $likeside, $isEscap);
        $returndata=array();
        if ($isStarAdd) {
            $returndata['*']="All";
        }
        foreach ($results as $data) {
            if (!empty($data->$key)) {
                $returndata[$data->$key]=$data->$value;
            }
        }
        return $returndata;
    }
    public function selectAllWithIdentity($unique_field, $select = "", $orderBy = "", $order = "", $limit = "", $limitStart = "", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isEscap = true)
    {
        $result=$this->selectAll($select, $orderBy, $order, $limit, $limitStart, $likefld, $likeValue, $extraParam, $likeside, $isEscap);
        if (count($result)>0) {
            $newrsult=array();
            foreach ($result as $obj) {
                if (!empty($obj->$unique_field)) {
                    $newrsult[$obj->$unique_field]=$obj;
                }
            }
            return $newrsult;
        }
        return $result;
    }
    public function selectAllWithIdentityWithSelectPropertyOnly($unique_field, $select = "", $orderBy = "", $order = "", $limit = "", $limitStart = "", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isEscap = true)
    {
        $result=$this->selectAll($select, $orderBy, $order, $limit, $limitStart, $likefld, $likeValue, $extraParam, $likeside, $isEscap, true);
        if (count($result)>0) {
            $newrsult=array();
            foreach ($result as $obj) {
                if (!empty($obj->$unique_field)) {
                    $newrsult[$obj->$unique_field]=$obj;
                }
            }
            return $newrsult;
        }
        return $result;
    }
    
    public function selectAllWithKeyValueWithStar($key, $value, $isStarAdd = true, $orderBy = "", $order = "", $limit = "", $limitStart = "", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isEscap = true)
    {
        $results=$this->selectAll($key.",".$value, $orderBy, $order, $limit, $limitStart, $likefld, $likeValue, $extraParam, $likeside, $isEscap);
        $returndata=array();
        if ($isStarAdd) {
            $returndata['*']="All";
        }
        foreach ($results as $data) {
            if (!empty($data->$key)) {
                $returndata[$data->$key]=$data->$value;
            }
        }
        return $returndata;
    }
    public function selectAllWithKeyValue($key, $value, $orderBy = "", $order = "", $limit = "", $limitStart = "", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isEscap = true)
    {
        return $this->selectAllWithKeyValueWithStar($key, $value, false, $orderBy, $order, $limit, $limitStart, $likefld, $likeValue, $extraParam, $likeside, $isEscap);
    }
    public function selectAllWithArrayKeys($key, $orderBy = "", $order = "", $limit = "", $limitStart = "", $likefld = "", $likeValue = "", $extraParam = array(), $likeside = "after", $isEscap = true)
    {
        $results=$this->selectAll($key, $orderBy, $order, $limit, $limitStart, $likefld, $likeValue, $extraParam, $likeside, $isEscap);
        $returndata=array();
        foreach ($results as $data) {
            if (!empty($data->$key)) {
                $returndata[]=$data->$key;
            }
        }
        return $returndata;
    }
    
    
    
    /**
     * @param strin $fieldName | db field name
     * @param string $default | default value
     * @return string
     */
    public function setNewIncId($fieldName, $default, $param = array())
    {
        $nthis=new static();
        if (is_array($param)&& count($param)>0) {
            foreach ($param as $property => $value) {
                if (property_exists($nthis, $property)) {
                    call_user_func(array($nthis, $property), $value);
                } else {
                    return false;
                }
            }
        }
        if (!$nthis->checkBasicCheck()) {
            return false;
        }
        if (!$nthis->isValidForm(false, false, true)) {
            return false;
        }
        if (!$nthis->setDBSelectWhereProperties(array(), true, true)) {
            return false;
        }
        $nthis->getSelectDB()->select("max({$fieldName}) as `lastS` ", false);
        $nthis->setJoinProperties();
        $result = $nthis->getSelectDB()->get($nthis->getTableName(false));
        if ($result && $result->num_rows() >0) {
            $row=$result->first_row();
            if ($row->lastS) {
                $a=$row->lastS;
                $a++;
                return $a;
            }
        }
        return $default;
    }
    public function selectQuery($sql, $isArray = false)
    {
        $result = $this->getSelectDB()->query($sql);
        if ($result) {
            if ($isArray) {
                return $result->result_array();
            }
            return $result->result();
        } else {
            return array ();
        }
    }
    
    public function selectQuery2($sql, $isArray = false)
    {
        $result = $this->getSelectDB()->query($sql);
        return $this->getSelectDB()->affected_rows();
    }
    
    public function isExists($property, $value, $otherParam = array())
    {
        if (property_exists($this, $property)) {
            $this->getSelectDB()->where($property, $value);
            foreach ($otherParam as $key => $pvalue) {
                $this->getSelectDB()->where($key, $pvalue);
            }
            $count=$this->getSelectDB()->count_all_results($this->tableName);
            if ($count) {
                if ($count > 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *
     * @return  CI_DB_query_builder
     */
    public function getSelectDB()
    {
        if (self::$db1 == null) {
            self::$db1 = $this->load->database("default", true);
        }
        return self::$db1;
    }
    
    /**
     *
     * @return  CI_DB_query_builder
     */
    public function getUpdateDB()
    {
        //if (!$this->config->item("IsMultipleDB")) {
            return $this->getSelectDB();
        //}
    
        /*if (self::$db2 == null) {
            self::$db2 = $this->load->database("update", true);
        }
        return self::$db2;*/
    }
    public static function __callStatic($func, $args)
    {
        if (static::startsWith($func, "FindBy")) {
            $funcl=strtolower($func);
            $property=str_replace("findby", "", $funcl);
            return static::findBy($property, $args[0]);
        }
        trigger_error("Call to undefined method ".get_called_class().": $func", E_USER_ERROR);
    }
    public function __call($func, $args)
    {
        
        if (isset($args [0])) {
            $value = $this->$func;
                
            // echo $func."=>".$value."==".$args[0];
            if (empty($args [1])) {
                $this->doFieldValueFilter($this->$func, $args [0]);
            }
            if ($value != $args [0] || ($args [0] == '' && $value == null)) {
                if (property_exists($this, $func)) {
                    if (isset($args [1])) {
                        $this->setOption [$func] = $args [1];
                    }
                    $this->setProperties [$func] = $args [0];
                }
                $this->$func = trim($args [0]);
            }
        } else {
            // echo $func;
        }
    }
    public function doFieldValueFilter($property, &$value, $isXsClean = true)
    {
    }
    public static function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
    public function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }
    public function countAll($likeFld = "", $likeValue = "", $extraParam = array(), $likeSide = "after")
    {
        if (empty($this->tableName)) {
            return false;
        }
        if (!$this->setDBSelectWhereProperties($extraParam, false, true)) {
            return false;
        }
        //set like
        $this->setDBLike($likeFld, $likeValue, $likeSide, true);
        //SetOrder
        $this->setJoinProperties(false);
      
        return $this->getSelectDB()->count_all_results($this->getTableName(false));
    }
    public function resetSetForInsetUpdate()
    {
        foreach ($this->setProperties as $key => $value) {
            if (isset($this->htmlInputField [$key])) {
                continue;
            }
            if (! empty($this->settedPropertyforLog)) {
                $this->settedPropertyforLog .= ", ";
            }
            $this->settedPropertyforLog .= $key."=".$value;
        }
        $this->castPropertyTypes($this->setProperties);
        $this->setProperties = array();
        $this->setOption     = array();
        $this->likesFields   = array();
        $this->group_by      = null;
    }
    public function castPropertyTypes(&$setProperties)
    {
        foreach ($setProperties as $property => $value) {
            if (!is_object($value) && isset($this->validations[$property])) {
                $rules = explode('|', $this->validations[$property]['Rule']);
                if (in_array('integer', $rules)) {
                    $this->{$property} = (int)$this->{$property};
                }
            }
        }
    }
    public function setFromArray(&$dataArray, $isNew = false, $selectedFields = null)
    {
        if ($selectedFields) {
            if (is_string($selectedFields)) {
                $selectedFields=explode(",", $selectedFields);
                array_walk($selectedFields, 'trim_value');
            }
        }
        foreach ($dataArray as $key => $value) {
            if (property_exists($this, $key) && (!$selectedFields || in_array($key, $selectedFields))) {
                $NewValue = $value;
                $oldValue = isset($dataArray["old_" . $key])?$dataArray["old_" . $key]:null;

                if ($oldValue != null) {
                    if ($oldValue == $NewValue) {
                        $this->$key = $NewValue;
                    } else {
                        $this->$key($NewValue);
                        // $this->SetValidationRule ( $key );
                    }
                } else {
                    if ($NewValue !== $oldValue) {
                        $this->$key($NewValue);
                    } else {
                        $this->$key = $NewValue;
                    }
                }
            }
        }
        return $this->isValidForm($isNew);
    }
    public function setFromPostData($isNew = false, $selectedFields = null)
    {
        return $this->setFromArray($_POST, $isNew, $selectedFields);
    }
    public function setWhereUpdate($property, $value, $isNotXSSClean = false)
    {
        $this->setWhereCondition($property, $value, $isNotXSSClean);
    }
    public function setWhereCondition($property, $value, $isNotXSSClean = false)
    {
        $this->updateWhereExtraField [$property] = $value;
        if ($isNotXSSClean) {
            $this->updateWhereExtraFieldOption[]=$property;
        }
    }
    public function getWhereConditionValue($property)
    {
        if (isset($this->updateWhereExtraField [$property])) {
            return $this->updateWhereExtraField [$property];
        }
        return null;
    }
    public function isSetDataForSaveUpdate($isShowMsg = false)
    {
        $re = count($this->setProperties) > 0;
        if (! $re && $isShowMsg) {
            AddError("No change for update");
        }
        return $re;
    }
    /**
     * @param String $properties, Comma separated
     * @return int
     */
    public function unsetAllExcepts($properties)
    {
        $properties=explode(",", $properties);
        $properties=array_map("trim", $properties);
        foreach ($this->setProperties as $key => $value) {
            if (!in_array($key, $properties)) {
                $this->unsetPrperty($key);
            }
        }
        return count($this->setProperties)>0;
    }
    public function isSetPrperty($property)
    {
        return isset($this->setProperties [$property]);
    }
    public function isSetWherePrperty($property)
    {
        return isset($this->updateWhereExtraField [$property]);
    }
    public function getWhereProperty($property)
    {
        return isset($this->updateWhereExtraField [$property])?$this->updateWhereExtraField [$property] :"";
    }
    public function hasPropertyOpt($property)
    {
        return isset($this->setOption [$property])?$this->setOption [$property] :false;
    }
    public function hasWherePrpertyOpt($property)
    {
        return isset($this->updateWhereExtraFieldOption [$property])?$this->updateWhereExtraFieldOption [$property] :false;
    }
    public function unsetPrperty($property)
    {
        if (isset($this->setProperties [$property])) {
            unset($this->setProperties [$property]);
        }
        if (isset($this->setOption [$property])) {
            unset($this->setOption [$property]);
        }
    }
    
    public function isHTMLProperty($property = "")
    {
        if (in_array($property, $this->htmlInputField)) {
            return true;
        }
        return false;
    }

    public static function getTotalQueries()
    {
        $ci=get_instance();
        ob_start();
        ?>
                <div class="row">
                    <div class="panel panel-info">
                        <div class="panel-heading">Queries</div>
                        <div class="panel-body">
                            <pre>
                                <?php
                                if (!empty(self::$db1)) {
                                    foreach (self::$db1->queries as $qur) {
                                        $qur=str_replace("\n", "", $qur);
                                        GPrint($qur);
                                    }
                                }
                                if (!empty(self::$db2)) {
                                    if ($ci->config->item("IsMultipleDB")) {
                                        foreach (self::$db2->queries as $qur) {
                                            $qur=str_replace("\n", "", $qur);
                                            GPrint($qur);
                                        }
                                    }
                                }
                                ?>
                            </pre>
                        </div>
                    </div>
                </div>
                <?php
                return ob_get_clean();
    }
    public static function getTotalQueriesForLog()
    {
        $ci=get_instance();
        ob_start();
        if (!empty(self::$db1)) {
            foreach (self::$db1->queries as $qur) {
                $qur=str_replace("\n", "", $qur);
                echo  ( $qur ),";\n";
            }
        }
        if (!empty(self::$db2)) {
            if ($ci->config->item("IsMultipleDB")) {
                foreach (self::$db2->queries as $qur) {
                    $qur=str_replace("\n", "", $qur);
                    echo  ( $qur ),";\n";
                }
            }
        }
            return ob_get_clean();
    }
    public static function getTotalQueriesCountStr()
    {
        $total=count(self::$db1->queries);
        if (!empty(self::$db2)) {
            $ci=get_instance();
            if ($ci->config->item("IsMultipleDB")) {
                $total+=count(self::$db2->queries);
            }
        }
        return "Total Quirie(s) = $total";
    }
        
    public function update($notLimit = false, $isShowMsg = true, $dontProcessIdWhereNotSet = true)
    {
        if ($this->isSetDataForSaveUpdate() && count($this->updateWhereExtraField) > 0) {
            if (! $this->isValidForm(false)) {
                return false;
            }
                //set update propertry for update
            if (!$this->setDBPropertyForInsertOrUpdate(true)) {
                return false;
            }
                    
                //set where condition propertry for update
            if (!$this->setDBUpdateWhereProperties(array(), $dontProcessIdWhereNotSet)) {
                return false;
            }
            if (!$notLimit) {
                $this->getUpdateDB()->limit(1);
            }
            if ($this->getUpdateDB()->update($this->tableName)) {
                if ($this->getUpdateDB()->affected_rows() > 0) {
                    $this->resetSetForInsetUpdate();
                    $this->unsetAllUpdateProperty();
                    $this->onSaveUpdateEvent();
                    return true;
                }
            } else {
                //AddError("Error-U005: Update failed. ");
            }
        } else {
            if ($isShowMsg && !$this->isSetDataForSaveUpdate()) {
                AddError("No data found for update");
            } elseif (count($this->updateWhereExtraField)==0) {
                add_model_errors_code("E004");
            }
        }
        return false;
    }
    protected static function deleteByKeyValue($key, $value, $noLimit = false)
    {
        $thisobj=new static();
        if (!property_exists($thisobj, $key)) {
            return false;
        }
        $thisobj->getUpdateDB()->where($key, $value);
        if (!$noLimit) {
            $thisobj->getUpdateDB()->limit(1);
        }
        if ($thisobj->getUpdateDB()->delete($thisobj->tableName)) {
            if ($thisobj->getUpdateDB()->affected_rows() > 0) {
                return true;
            }
        }
        return false;
    }
    public function getAffectedRows($isSelectDB = false)
    {
        if ($isSelectDB) {
            return  $this->getSelectDB()->affected_rows();
        } else {
            return  $this->getUpdateDB()->affected_rows();
        }
    }
    public function forceSetPkForUpdate($isClean = true)
    {
        $pk = $this->primaryKey;
        if (!empty($this->$pk)) {
            if (!$isClean) {
                $this->getUpdateDB()->set($pk.$this->$pk, false);
            } else {
                $this->getUpdateDB()->set($pk, $this->$pk, false);
            }
        }
    }
        
    public function getTextByKey($property, $isTag = true, $key = null)
    {
        if ($isTag) {
            $data=$this->getPropertyOptionsTag($property);
        } else {
            $data=$this->getPropertyOptions($property);
        }
        if (!empty($key) || property_exists($this, $property)) {
            if (empty($key)) {
                $key=$this->$property;
            }
            return !empty($data[$key])?$data[$key]:$key;
        } else {
            return "Undefined Property";
        }
    }
    public static function getDBFields()
    {
        $thisobj=new static();
        $fields = $thisobj->getSelectDB()->field_data($thisobj->tableName);
        $returnField=[];
        foreach ($fields as $fld) {
            $returnField[$fld->name]=$fld;
        }
        return $returnField;
    }
    public static function dbColumnAddOrModify($columnName, $type, $length, $default = '', $nullstatus = 'NOT NULL', $after = '', $comment = '', $char_set = '')
    {
        $thisObj = new static();
        $tableName = $thisObj->tableName;
        if (empty($tableName)) {
            return;
        }
        if ($default == '') {
            $default = "''";
        }
        if (!empty($char_set)) {
            $char_set = " CHARACTER SET {$char_set}";
        }
        if (!empty($after)) {
            $after = " AFTER {$after}";
        }
        $fields = static::getDBFields();
        //GPrint($fields);
        if (isset($fields[$columnName])) {
            $queryType = "MODIFY";
        } else {
            $queryType = "ADD";
        }
        if (strtolower($type) == "text") {
            $query = "ALTER TABLE `{$tableName}` {$queryType} COLUMN `{$columnName}`  {$type} $char_set {$nullstatus}  COMMENT '{$comment}' $after";
        } elseif (strtolower($type) == "timestamp") {
            if ($default=="''") {
                $default="'0000-00-00 00:00:00'";
            }
            $query = "ALTER TABLE `{$tableName}` {$queryType} COLUMN `{$columnName}`  {$type} {$nullstatus} DEFAULT $default $after";
        } else {
            $query = "ALTER TABLE `{$tableName}` {$queryType} COLUMN `{$columnName}`  {$type}({$length}) $char_set {$nullstatus} DEFAULT {$default} COMMENT '{$comment}' $after";
        }
        //echo ($query) . "<br/>"; die;return;
            
        $thisObj->getUpdateDB()->query($query);
    }
    public static function dbAddIndex($key_name, $fields, $isUnique = false)
    {
        $thisObj = new static();
        $tableName = $thisObj->tableName;
        if (empty($tableName)) {
            return;
        }
        $allIndex="SHOW INDEX FROM $tableName where key_name='$key_name'";
        $result=$thisObj->selectQuery($allIndex);
        if (is_array($result) && count($result)>0) {
            $dropindex="ALTER TABLE $tableName DROP INDEX `$key_name`";
            $thisObj->selectQuery2($dropindex);
        }
        $type=$isUnique?"UNIQUE":"INDEX";
        $query="ALTER TABLE `$tableName` ADD  $type `$key_name` ($fields)";
        $thisObj->getUpdateDB()->query($query);
    }
    
    /**
     * @param string $textDomain
     */
    public function setTextDomain($textDomain)
    {
        $this->textDomain = $textDomain;
    }

}
    


