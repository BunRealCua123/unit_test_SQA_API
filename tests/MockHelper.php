<?php

namespace Tests;

/**
 * Helper class for mocking core classes used in testing
 */
class MockHelper
{
    /**
     * Create a mock Controller model for testing
     */
    public static function mockModel($className, $id, $isAvailable = true, $data = [])
    {
        // Create a mock model that behaves like a real model
        $model = new \stdClass();
        $model->id = $id;
        $model->data = $data;

        // Add isAvailable method
        $model->isAvailable = function () use ($isAvailable) {
            return $isAvailable;
        };

        // Add get method
        $model->get = function ($key) use ($data, $id) {
            if ($key === 'id') return $id;
            return isset($data[$key]) ? $data[$key] : null;
        };

        // Add set method
        $model->set = function ($key, $value) use (&$data, $model) {
            $data[$key] = $value;
            return $model;
        };

        // Add update method
        $model->update = function () use ($model) {
            return $model;
        };

        // Add save method
        $model->save = function () use ($model) {
            return true;
        };

        // Add delete method
        $model->delete = function () use ($model) {
            return true;
        };

        return $model;
    }

    /**
     * Set up a mock response object for controller tests
     */
    public static function mockResponse()
    {
        $resp = new \stdClass();
        $resp->result = 0;
        $resp->msg = '';
        $resp->data = null;

        return $resp;
    }

    /**
     * Mock the DB class for database queries
     */
    public static function mockDB($results = [])
    {
        $db = new \stdClass();
        $db->query_conditions = [];
        $db->results = $results;
        $db->current_table = '';

        // Mock table method
        $db->table = function ($tableName) use ($db) {
            $db->current_table = $tableName;
            $db->query_conditions['table'] = $tableName;
            return $db;
        };

        // Mock select method
        $db->select = function ($columns = "*") use ($db) {
            $db->query_conditions['select'] = $columns;
            return $db;
        };

        // Mock where method
        $db->where = function ($column, $operator = null, $value = null) use ($db) {
            if (!isset($db->query_conditions['where'])) {
                $db->query_conditions['where'] = [];
            }

            $db->query_conditions['where'][] = [
                'column' => $column,
                'operator' => $operator,
                'value' => $value
            ];

            return $db;
        };

        // Mock orderBy method
        $db->orderBy = function ($column, $direction = 'asc') use ($db) {
            if (!isset($db->query_conditions['orderBy'])) {
                $db->query_conditions['orderBy'] = [];
            }

            $db->query_conditions['orderBy'][] = [
                'column' => $column,
                'direction' => $direction
            ];

            return $db;
        };

        // Mock limit method
        $db->limit = function ($limit, $offset = 0) use ($db) {
            $db->query_conditions['limit'] = $limit;
            $db->query_conditions['offset'] = $offset;
            return $db;
        };

        // Mock get method to return results
        $db->get = function () use ($db) {
            return $db->results;
        };

        // Mock count method
        $db->count = function () use ($db) {
            return count($db->results);
        };

        // Mock leftJoin method
        $db->leftJoin = function ($table, $first, $operator, $second) use ($db) {
            return $db;
        };

        // Mock insert method
        $db->insert = function ($data) use ($db) {
            return 1; // Return an ID
        };

        // Mock update method
        $db->update = function ($data) use ($db) {
            return 1; // Return number of affected rows
        };

        // Mock delete method
        $db->delete = function () use ($db) {
            return 1; // Return number of affected rows
        };

        return $db;
    }

    /**
     * Create a global function override
     */
    public static function setupGlobalFunctionMocks()
    {
        // Override global functions used in tests
        if (!function_exists('isVietnameseName')) {
            function isVietnameseName($name)
            {
                return true; // Always return valid for testing
            }
        }

        if (!function_exists('isNumber')) {
            function isNumber($number)
            {
                return is_numeric($number);
            }
        }

        if (!function_exists('isAppointmentTimeValid')) {
            function isAppointmentTimeValid($time)
            {
                return '';
            }
        }

        if (!function_exists('isBirthdayValid')) {
            function isBirthdayValid($birthday)
            {
                return '';
            }
        }
    }
}
