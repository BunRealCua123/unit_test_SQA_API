<?php
// filepath: d:\xampp1\htdocs\PTIT-Do-An-Tot-Nghiep\api\tests\TestCase.php
namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base TestCase class for all tests
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Holds original globals
     * @var array
     */
    protected $globals_backup;

    /**
     * Setup common functionality for tests
     */
    protected function setUp(): void  // Sửa thành void để tương thích với PHPUnit 9.x
    {
        parent::setUp();

        // Backup globals
        $this->globals_backup = [
            '_GET' => isset($_GET) ? $_GET : [],
            '_POST' => isset($_POST) ? $_POST : [],
            '_REQUEST' => isset($_REQUEST) ? $_REQUEST : [],
            '_SERVER' => isset($_SERVER) ? $_SERVER : []
        ];

        // Set the SERVER method to GET by default
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Setup function mocks
        MockHelper::setupGlobalFunctionMocks();

        // Override DB class methods
        $GLOBALS['_MOCK_DB'] = MockHelper::mockDB();
    }

    /**
     * Cleanup after tests
     */
    protected function tearDown(): void  // Sửa thành void để tương thích với PHPUnit 9.x
    {
        parent::tearDown();

        // Restore globals
        $_GET = $this->globals_backup['_GET'];
        $_POST = $this->globals_backup['_POST'];
        $_REQUEST = $this->globals_backup['_REQUEST'];
        $_SERVER = $this->globals_backup['_SERVER'];

        // Clear static mock data
        unset($GLOBALS['_MOCK_MODELS']);
        unset($GLOBALS['_MOCK_DB']);
    }

    // Giữ nguyên các phương thức còn lại...

    /**
     * Set up the controller for testing with dependency injection
     */
    protected function setupController($controller, $variables = [])
    {
        foreach ($variables as $key => $value) {
            $controller->setVariable($key, $value);
        }

        return $controller;
    }

    /**
     * Create a mock authenticated user
     */
    protected function createMockAuthUser($role = 'admin', $attributes = [])
    {
        $userAttributes = array_merge([
            'id' => 1,
            'role' => $role,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'active' => 1
        ], $attributes);

        // Create a mock user
        $mockUser = MockHelper::mockModel('User', $userAttributes['id'], true, $userAttributes);

        return $mockUser;
    }

    /**
     * Mock Input class for HTTP method
     */
    protected function mockHttpMethod($method)
    {
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
    }

    /**
     * Mock Input class method
     */
    protected function mockInputMethod($method)
    {
        $this->mockHttpMethod($method);

        // Create override for Input::method()
        $GLOBALS['_MOCK_INPUT_METHOD'] = strtoupper($method);
    }

    /**
     * Mock GET request parameters
     */
    protected function mockGet($params = [])
    {
        $_GET = $params;
        $_REQUEST = array_merge($_REQUEST, $params);
    }

    /**
     * Mock POST request parameters
     */
    protected function mockPost($params = [])
    {
        $_POST = $params;
        $_REQUEST = array_merge($_REQUEST, $params);
    }

    /**
     * Create a mock Route object with parameters
     */
    protected function createMockRoute($params = [])
    {
        $route = new \stdClass();
        $route->params = (object)$params;
        return $route;
    }

    /**
     * Call a private method on a controller using reflection
     */
    protected function callPrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Mock Controller::model method to return predefined models
     */
    protected function mockModelMethod($modelName, $mockModel)
    {
        if (!isset($GLOBALS['_MOCK_MODELS'])) {
            $GLOBALS['_MOCK_MODELS'] = [];
        }
        $GLOBALS['_MOCK_MODELS'][$modelName] = $mockModel;
    }
}
