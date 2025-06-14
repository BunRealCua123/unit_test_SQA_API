<?php
// filepath: d:\xampp1\htdocs\PTIT-Do-An-Tot-Nghiep\api\tests\Mocks\DBMock.php
namespace Tests\Mocks;

class DBMock
{
    public static function table($table)
    {
        if (isset($GLOBALS['_MOCK_DB'])) {
            return $GLOBALS['_MOCK_DB']->table($table);
        }
        return null;
    }
}
