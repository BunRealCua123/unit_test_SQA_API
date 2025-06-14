<?php
// filepath: d:\xampp1\htdocs\PTIT-Do-An-Tot-Nghiep\api\tests\Mocks\InputMock.php
namespace Tests\Mocks;

class InputMock
{
    public static function method()
    {
        if (isset($GLOBALS['_MOCK_INPUT_METHOD'])) {
            return $GLOBALS['_MOCK_INPUT_METHOD'];
        }
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }

    public static function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public static function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    public static function put($key = null, $default = null)
    {
        if (self::method() !== 'PUT') {
            return $default;
        }
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }

    public static function patch($key = null, $default = null)
    {
        if (self::method() !== 'PATCH') {
            return $default;
        }
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }
}
