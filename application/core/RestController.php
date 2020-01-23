<?php
/**
 * @since: 23/01/2020
 * @author: Sarwar Hasan
 * @version 1.0.0
 */
namespace App\core;

/**
 * This is a base controller for Rest API
 * @property \CI_Router router
 * @property \CI_Output output
 */
class RestController extends AppController
{
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_INTERNAL_ERROR = 500;
    protected $method;
    public $input;

    public function __construct()
    {
        parent::__construct();
        $this->input= new \CI_Input();

        $this->method=$this->input->method();
    }

    /**
     * It will return the request param array
     * @return array
     */
    protected function getParams()
    {


        /* Input data comes in on the stdin stream */
        $inputData = fopen("php://input", "r");

        $raw_data = '';

        /* Read the data 1 KB at a time
           and write to the file */
        while ($chunk = fread($inputData, 1024)) {
            $raw_data .= $chunk;
        }

        /* Close the streams */
        fclose($inputData);

        // Fetch content and determine boundary
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        if (empty($boundary)) {
            parse_str($raw_data, $data);
             $data;
            return $data;
        }

        // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") {
                break;
            }

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                $tmp_name = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                //Parse File
                if (isset($matches[4])) {
                    //if labeled the same as previous, skip
                    if (isset($_FILES[ $matches[ 2 ] ])) {
                        continue;
                    }

                    //get filename
                    $filename = $matches[4];

                    //get tmp name
                    $filename_parts = pathinfo($filename);
                    $tmp_name = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);

                    //populate $_FILES with information, size may be off in multibyte situation
                    $_FILES[ $matches[ 2 ] ] = array(
                        'error'=>0,
                        'name'=>$filename,
                        'tmp_name'=>$tmp_name,
                        'size'=>strlen($body),
                        'type'=>$value
                    );

                    //place in temporary directory
                    file_put_contents($tmp_name, $body);
                }

                //Parse Field
                else {
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                }
            }
        }
        return $data;
    }


    /**
     * Requests are not made to methods directly, the request will be for
     * an "object". This simply maps the object and method to the correct
     * Controller method.
     *
     * @param string $object_called
     * @param array  $arguments     The arguments passed to the controller method
     *
     * @throws Exception
     */
    public function _remap($object_called, $arguments = [])
    {

        if ($this->isAuthorized()) {
            if (method_exists($this, $this->method)) {
                call_user_func_array(array($this, $this->method), $arguments);
            } else {
                call_user_func_array(array($this, "unknown"), $arguments);
            }
        } else {
            call_user_func_array(array($this, "unauthorize"), $arguments);
        }
    }

    /**
     * This method is for authorize the request
     * @return bool
     */
    protected function isAuthorized()
    {
        //You can add logic or it can be override in child class
        return true;
    }

    /**
     * It display the API response
     * @param null $data
     * @param null $httpCode
     * @param string $httpText
     *
     * @return \CI_Output
     */
    protected function response($data = null, $httpCode = null, $httpText = '')
    {

        $this->output->set_status_header($httpCode, $httpText);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($httpCode, $httpText) // Return status
            ->set_output(json_encode($data));
    }

    /**
     * it will diplay error response;
     * @param $httpCode
     */
    public function errorResponse($httpCode)
    {
        $data=new \stdClass();
        $data->code=$httpCode;
        $data->status=false;
        $message=implode(",", self::$errors);
        $data->msg=rtrim($message, ",");
        $this->response($data, $httpCode);
    }

    /**
     * It will be display when unknow method request.
     */
    public function unknown()
    {
        $data=new \stdClass();
        $data->code=self::HTTP_NOT_FOUND;
        $data->status=false;
        $data->msg="Unknown method";
        $this->response($data, self::HTTP_NOT_FOUND);
    }

    /**
     * It will forcely set the internal error response
     */
    public function setInternalError()
    {
        $this->method="internalError";
    }

    /**
     * it will be display when there will any internal error
     */
    public function internalError()
    {
        $data=new \stdClass();
        $data->code=self::HTTP_INTERNAL_ERROR;
        $data->status=false;
        $data->msg="Unexpected errors";
        $this->response($data, self::HTTP_INTERNAL_ERROR);
    }

    /**
     * It will display when request will be unauthorized
     */
    public function unauthorize()
    {
        $data=new \stdClass();
        $data->code=self::HTTP_NOT_FOUND;
        $data->status=false;
        $data->msg="Unauthorized";
        $this->response($data, self::HTTP_UNAUTHORIZED);
    }
}
