<?php
/**
 * @since: 23/01/2020
 * @author: Sarwar Hasan
 * @version 1.0.0
 */
namespace App\libraries;

class RemoteUser
{
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $created_at;
    public $updated_at;

    /**
     * it will return a remote user object from general object, it uses reference to reduce the memory
     * @param $generalObject
     *
     * @return RemoteUser
     */
    public static function getFromGeneralObject(&$generalObject)
    {
        $newObject=new self();
        if (is_object($generalObject)) {
            foreach ($generalObject as $key => $val) {
                if (property_exists($newObject, $key)) {
                    $newObject->{$key}=$val;
                }
            }
        }
        return $newObject;
    }
}
