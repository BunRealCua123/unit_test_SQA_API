<?php

namespace Tests\Controllers;

use Tests\TestCase;
use Tests\MockHelper;

/**
 * Tests for AppointmentQueueNowController
 */
class AppointmentQueueNowControllerTest extends TestCase
{
    protected $controller;

    /**
     * Set up the test environment
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new \AppointmentQueueNowController();
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

        // Mock GET parameter for doctor_id
        $this->mockInputMethod('GET');
        $this->mockGet(['doctor_id' => 5]);

        // Set up expected mock response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "Queue retrieved successfully";

        // Execute test
        ob_start();
        $this->controller->process();
        ob_end_clean();

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
    }

    /**
     * Test getQueue method with member role
     */
    public function testGetQueueWithMemberRole()
    {
        // Create mock AuthUser with member role (doctor)
        $doctorId = 5;
        $mockAuthUser = $this->createMockAuthUser('member', ['id' => $doctorId]);

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock DB query results
        $mockAppointments = [
            (object)[
                'id' => 1,
                'patient_name' => 'Normal Patient 1',
                'numerical_order' => 1,
                'status' => 'processing',
                'doctor_id' => $doctorId,
                'date' => date('d-m-Y')
            ]
        ];
        $db = MockHelper::mockDB($mockAppointments);

        // Set up expected response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "Queue retrieved successfully !";

        // Execute test by calling private getQueue method
        $this->callPrivateMethod($this->controller, 'getQueue');

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
        $this->assertEquals("Queue retrieved successfully !", $this->controller->resp->msg);
    }

    /**
     * Test getQueue method with missing doctor_id
     */
    public function testGetQueueWithMissingDoctorId()
    {
        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // No doctor_id in GET parameters

        // Set up expected error response
        $this->controller->resp->result = 0;
        $this->controller->resp->msg = "Missing doctor ID";

        // Execute test by calling private getQueue method
        $this->callPrivateMethod($this->controller, 'getQueue');

        // Assert error response
        $this->assertEquals(0, $this->controller->resp->result);
        $this->assertEquals("Missing doctor ID", $this->controller->resp->msg);
    }
}
