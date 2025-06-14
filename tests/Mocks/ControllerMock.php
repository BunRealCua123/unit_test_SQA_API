<?php
// filepath: d:\xampp1\htdocs\PTIT-Do-An-Tot-Nghiep\api\tests\Mocks\ControllerMock.php

class Controller
{
    public $variables = [];
    public $resp;

    public function __construct($variables = [])
    {
        $this->variables = $variables;
        $this->resp = new \stdClass();
        $this->resp->result = 0;
        $this->resp->data = null;
        $this->resp->msg = '';
    }

    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;
        return $this;
    }

    public function getVariable($key)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : null;
    }

    public static function model($name, $args = [])
    {
        // Return mock model if defined
        if (isset($GLOBALS['_MOCK_MODELS']) && isset($GLOBALS['_MOCK_MODELS'][$name])) {
            return $GLOBALS['_MOCK_MODELS'][$name];
        }

        // If we're in test environment, create a mock
        if (defined('TESTING') && TESTING === true) {
            return \Tests\MockHelper::mockModel($name, $args);
        }

        // Otherwise try to load the real model
        if (is_array($name)) {
            if (count($name) != 2) {
                throw new \Exception('Invalid parameter');
            }

            $file = $name[0];
            $class = $name[1];
        } else {
            $file = APPPATH . "/models/" . $name . "Model.php";
            $class = $name . "Model";
        }

        if (file_exists($file)) {
            require_once $file;

            if (!is_array($args)) {
                $args = array($args);
            }

            $reflector = new \ReflectionClass($class);
            return $reflector->newInstanceArgs($args);
        }

        return null;
    }

    protected function jsonecho($resp = null)
    {
        if (is_null($resp)) {
            $resp = $this->resp;
        }

        echo \Input::get("callback") ?
            \Input::get("callback") . "(" . json_encode($resp) . ")" :
            json_encode($resp);
        exit;
    }
}
