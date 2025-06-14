<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Define any constants needed for testing
if (!defined('APPURL')) {
    define('APPURL', 'http://localhost:8080/PTIT-Do-An-Tot-Nghiep/api');
}

if (!defined('APPPATH')) {
    define('APPPATH', __DIR__ . '/../app');
}

if (!defined('TABLE_PREFIX')) {
    define('TABLE_PREFIX', '');
}

// Define tables constants if not already defined
if (!defined('TABLE_APPOINTMENTS')) {
    define('TABLE_APPOINTMENTS', 'appointments');
}
if (!defined('TABLE_DOCTORS')) {
    define('TABLE_DOCTORS', 'doctors');
}
if (!defined('TABLE_PATIENTS')) {
    define('TABLE_PATIENTS', 'patients');
}
if (!defined('TABLE_SERVICES')) {
    define('TABLE_SERVICES', 'services');
}
if (!defined('TABLE_SPECIALITIES')) {
    define('TABLE_SPECIALITIES', 'specialities');
}
if (!defined('TABLE_ROOMS')) {
    define('TABLE_ROOMS', 'rooms');
}

// Define database connection constants
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', '3307');
}
if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', 'doantotnghiep');
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}

// Set date timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include mock class files
require_once __DIR__ . '/Mocks/DBMock.php';
require_once __DIR__ . '/Mocks/InputMock.php';

// Create class aliases
class_alias('Tests\Mocks\DBMock', 'DB');
class_alias('Tests\Mocks\InputMock', 'Input');

// Mock Controller class for tests
if (!class_exists('Controller')) {
    require_once __DIR__ . '/Mocks/ControllerMock.php';
}

// Mock helper functions if they don't exist
if (!function_exists('isVietnameseName')) {
    function isVietnameseName($name)
    {
        return true;
    }
}

if (!function_exists('isNumber')) {
    function isNumber($number)
    {
        return is_numeric($number);
    }
}

if (!function_exists('isBirthdayValid')) {
    function isBirthdayValid($birthday)
    {
        return '';
    }
}

if (!function_exists('isAppointmentTimeValid')) {
    function isAppointmentTimeValid($time)
    {
        return '';
    }
}

// Autoload app classes (controllers, models, etc)
spl_autoload_register(function ($class_name) {
    // Remove namespace if present
    if (strpos($class_name, '\\') !== false) {
        $parts = explode('\\', $class_name);
        $class_name = end($parts);
    }

    // Check controllers
    if (file_exists(APPPATH . '/controllers/' . $class_name . '.php')) {
        require_once APPPATH . '/controllers/' . $class_name . '.php';
        return true;
    }

    // Check models
    if (file_exists(APPPATH . '/models/' . $class_name . '.php')) {
        require_once APPPATH . '/models/' . $class_name . '.php';
        return true;
    }

    // Check core
    if (file_exists(APPPATH . '/core/' . $class_name . '.php')) {
        require_once APPPATH . '/core/' . $class_name . '.php';
        return true;
    }

    return false;
});
