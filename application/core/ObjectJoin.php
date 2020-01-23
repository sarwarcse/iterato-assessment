<?php
/**
 * @since: 23/OCT/2016
 * @author: Sarwar Hasan
 * @version 1.0.0
 */
namespace app\AppModel;

class ObjectJoin
{
    const LEFT="LEFT";
    const RIGHT="RIGHT";
    const OUTER="OUTER";
    const INNER="INNER";
    public $join_obj_property;
    public $main_obj_property;
    /**
     * @var APP_Model
     */
    public $join_obj;
    public $type;
    public $extra_param=[];
}