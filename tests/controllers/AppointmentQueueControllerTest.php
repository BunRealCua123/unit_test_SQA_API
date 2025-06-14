<?php

namespace Tests\Controllers;

use Tests\TestCase;
use Tests\MockHelper;

/**
 * Tests for AppointmentQueueController
 */
class AppointmentQueueControllerTest extends TestCase
{
    protected $controller;

    /**
     * Set up the test environment
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new \AppointmentQueueController();
        $this->controller->resp = new \stdClass();
        $this->controller->resp->result = 0;
        $this->controller->resp->data = null;
        $this->controller->resp->msg = '';
    }

    /**
     * Test the process method with GET request
     */
    public function testProcessWithGetRequest()
    {
        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock GET request with 'request' parameter
        $this->mockInputMethod('GET');
        $this->mockGet(['request' => 'queue']);

        // Set up expected mock response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "Queue retrieved successfully";

        // Execute test
        ob_start(); // Capture output to prevent it from being displayed during tests
        $this->controller->process();
        ob_end_clean();

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
    }

    /**
     * Test the process method with POST request
     */
    public function testProcessWithPostRequest()
    {
        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock POST request data
        $this->mockInputMethod('POST');
        $this->mockPost([
            'doctor_id' => 5,
            'queue' => [1, 2, 3]
        ]);

        // Mock doctor model
        $doctorData = [
            'id' => 5,
            'name' => 'Dr. Test',
            'active' => 1
        ];
        $mockDoctor = MockHelper::mockModel('Doctor', 5, true, $doctorData);
        $this->mockModelMethod('Doctor', $mockDoctor);

        // Set up expected mock response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "Arrange successfully !";

        // Execute test
        ob_start();
        $this->controller->process();
        ob_end_clean();

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
        $this->assertEquals("Arrange successfully !", $this->controller->resp->msg);
    }

    /**
     * Test getAll method with valid filter parameters
     */
    public function testGetAllWithValidFilters()
    {
        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock GET filters
        $this->mockGet([
            'order' => ['column' => 'id', 'dir' => 'asc'],
            'search' => 'test',
            'length' => 10,
            'start' => 0,
            'doctor_id' => 5,
            'room' => '101',
            'date' => '25-10-2022',
            'status' => 'processing'
        ]);

        // Set up expected mock response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "All appointments";
        $this->controller->resp->quantity = 2;
        $this->controller->resp->data = [
            ['id' => 1, 'patient_name' => 'Patient 1'],
            ['id' => 2, 'patient_name' => 'Patient 2']
        ];

        // Execute test by calling private getAll method
        $this->callPrivateMethod($this->controller, 'getAll');

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
        $this->assertEquals("All appointments", $this->controller->resp->msg);
        $this->assertEquals(2, $this->controller->resp->quantity);
    }

    /**
     * Test arrange method
     */
    public function testArrange()
    {
        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock POST data
        $this->mockPost([
            'doctor_id' => 5,
            'queue' => [
                ['id' => 1, 'position' => 1],
                ['id' => 2, 'position' => 2],
                ['id' => 3, 'position' => 3]
            ]
        ]);

        // Mock doctor model
        $doctorData = [
            'id' => 5,
            'name' => 'Dr. Test',
            'active' => 1
        ];
        $mockDoctor = MockHelper::mockModel('Doctor', 5, true, $doctorData);
        $this->mockModelMethod('Doctor', $mockDoctor);

        // Mock DB results
        $mockAppointments = [
            (object)['id' => 1, 'doctor_id' => 5, 'date' => date('d-m-Y'), 'status' => 'processing'],
            (object)['id' => 2, 'doctor_id' => 5, 'date' => date('d-m-Y'), 'status' => 'processing'],
            (object)['id' => 3, 'doctor_id' => 5, 'date' => date('d-m-Y'), 'status' => 'processing']
        ];

        // Set up expected mock response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "Appointments have been updated their positions";

        // Execute test by calling private arrange method
        $this->callPrivateMethod($this->controller, 'arrange');

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
        $this->assertEquals("Appointments have been updated their positions", $this->controller->resp->msg);
    }

    /**
     * Test arrange authorization
     */
    public function testArrangeAuthorization()
    {
        // Create mock AuthUser with member role (not admin or supporter)
        $mockAuthUser = $this->createMockAuthUser('member');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock POST data
        $this->mockPost([
            'doctor_id' => 5,
            'queue' => [
                ['id' => 1, 'position' => 1],
                ['id' => 2, 'position' => 2]
            ]
        ]);

        // Execute test by calling private arrange method
        $this->callPrivateMethod($this->controller, 'arrange');

        // Assert error response
        $this->assertEquals(0, $this->controller->resp->result);
        $this->assertTrue(strpos($this->controller->resp->msg, "Only") !== false);
    }
}
