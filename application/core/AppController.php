<?php
/**
 * @since: 23/01/2020
 * @author: Sarwar Hasan
 * @version 1.0.0
 */
namespace App\core;

class AppController extends \CI_Controller
{
    private $viewData=[];
    protected static $errors=[];
    public $uri;

    public function __construct()
    {
        parent::__construct();
        $this->uri= new \CI_URI();
    }

    public function addViewData($name, $val)
    {
    }
    private function getDefaultViewPath()
    {
        $dir=strtolower($this->router->directory);
        $ctrl=strtolower($this->router->class);
        $method=strtolower($this->router->method);
        $dirname = "";
        if (! empty($dir)) {
            $dirname = rtrim($dir, DIRECTORY_SEPARATOR."/");
        }
        $viewName = $dirname."/".(! empty($ctrl)? $ctrl."/" : "").$method;
        return $viewName;
    }
    private function filterData($key, &$value, $allowList = [])
    {
        $preg="/\-\-|[;'\"]|eval[^a-z]|cast\s*\(|base64_decode|sleep[^a-z]|gzinflate|XOR|str_rot13|javascript|\\\+|<|>/i";
        //it clear -- ; ' " eval cast base64_decode gzinflate str_rot13 javascript
        if (!in_array($key, $allowList)) {
            if (!empty($value)) {
                if (is_string($value)) {
                    $value=preg_replace($preg, "", $value);
                } elseif (is_array($value)) {
                    foreach ($value as $k => &$v) {
                        $this->filterData($k, $v, $allowList);
                    }
                }
            }
        }
    }
    protected function filterArray(&$dataArray, $allowList = [])
    {
        foreach ($dataArray as $key => &$data) {
            $this->filterData($key, $data, $allowList);
        }
    }
    public function display($viewName = '')
    {
        if (empty($viewName)) {
            $viewName=$this->getDefaultViewPath();
        }
        $output=$this->load->view($viewName, $this->viewData, true);
        $this->load->view("themes/main", ['output'=>$output]);
    }
    public static function addError($message)
    {
        $hash=hash('crc32b', $message);
        self::$errors[$hash]=$message;
    }
    public static function getErrors()
    {
        return self::$errors;
    }
}
