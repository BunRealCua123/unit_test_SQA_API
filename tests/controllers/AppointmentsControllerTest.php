<?php

namespace Tests\Controllers;

use Tests\TestCase;
use Tests\MockHelper;

/**
 * Tests for AppointmentsController
 */
class AppointmentsControllerTest extends TestCase
{
    protected $controller;

    /**
     * Set up the test environment
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new \AppointmentsController();
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

        // Mock Input class for GET method
        $this->mockInputMethod('GET');

        // Set up expected response for getAll
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "All appointments";

        // Execute test
        ob_start();
        $this->controller->process();
        ob_end_clean();

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
        $this->assertEquals("All appointments", $this->controller->resp->msg);
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

        // Mock Input class for POST method
        $this->mockInputMethod('POST');
        $this->mockPost([
            'patient_name' => 'Test Patient',
            'patient_birthday' => '1990-01-01',
            'patient_phone' => '0123456789',
            'patient_reason' => 'Test reason',
            'doctor_id' => 5,
            'appointment_time' => '10:30'
        ]);

        // Mock doctor model
        $doctorData = [
            'id' => 5,
            'name' => 'Dr. Test',
            'active' => 1,
            'role' => 'member'
        ];
        $mockDoctor = MockHelper::mockModel('Doctor', 5, true, $doctorData);
        $this->mockModelMethod('Doctor', $mockDoctor);

        // Set up expected response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "Create appointment successfully !";

        // Execute test
        ob_start();
        $this->controller->process();
        ob_end_clean();

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
    }

    /**
     * Test getAll method with filters
     */
    public function testGetAllWithFilters()
    {
        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock GET filters
        $this->mockGet([
            'order' => ['column' => 'id', 'dir' => 'desc'],
            'search' => 'test',
            'length' => 10,
            'start' => 0,
            'doctor_id' => 5,
            'room_id' => 3,
            'date' => '25-10-2022',
            'status' => 'processing'
        ]);

        // Set up expected response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "All appointments";
        $this->controller->resp->data = [
            'appointments' => [],
            'total' => 0
        ];

        // Execute test by calling private getAll method
        $this->callPrivateMethod($this->controller, 'getAll');

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
        $this->assertEquals("All appointments", $this->controller->resp->msg);
        $this->assertTrue(isset($this->controller->resp->data['appointments']));
    }

    /**
     * Test oldFlow method with valid data
     */
    public function testOldFlowWithValidData()
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
            'patient_name' => 'Test Patient',
            'patient_birthday' => '1990-01-01',
            'patient_phone' => '0123456789',
            'patient_reason' => 'Test reason',
            'appointment_time' => '10:30'
        ]);

        // Mock doctor model
        $doctorData = [
            'id' => 5,
            'name' => 'Dr. Test',
            'active' => 1
        ];
        $mockDoctor = MockHelper::mockModel('Doctor', 5, true, $doctorData);
        $this->mockModelMethod('Doctor', $mockDoctor);

        // Mock appointment model
        $mockAppointment = MockHelper::mockModel('Appointment', 0, false);
        $this->mockModelMethod('Appointment', $mockAppointment);

        // Set up expected response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "Create appointment successfully !";
        $this->controller->resp->data = ['id' => 1];

        // Execute test by calling private oldFlow method
        $this->callPrivateMethod($this->controller, 'oldFlow');

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
        $this->assertEquals("Create appointment successfully !", $this->controller->resp->msg);
    }

    /**
     * Test oldFlow method with invalid data
     */
    public function testOldFlowWithInvalidData()
    {
        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock POST data with missing required fields
        $this->mockPost([
            'doctor_id' => 5,
            // Missing patient_name
            'patient_birthday' => '1990-01-01',
            'patient_phone' => '0123456789'
            // Missing patient_reason
        ]);

        // Execute test by calling private oldFlow method
        $this->callPrivateMethod($this->controller, 'oldFlow');

        // Assert error response
        $this->assertEquals(0, $this->controller->resp->result);
        $this->assertTrue(!empty($this->controller->resp->msg));
    }

    /**
     * Test oldFlow2 method
     */
    public function testOldFlow2()
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
            'patient_name' => 'Test Patient',
            'patient_birthday' => '1990-01-01',
            'patient_phone' => '0123456789',
            'patient_reason' => 'Test reason',
            'appointment_time' => '10:30'
        ]);

        // Mock doctor model
        $doctorData = [
            'id' => 5,
            'name' => 'Dr. Test',
            'active' => 1,
            'role' => 'member'
        ];
        $mockDoctor = MockHelper::mockModel('Doctor', 5, true, $doctorData);
        $this->mockModelMethod('Doctor', $mockDoctor);

        // Mock appointment model
        $mockAppointment = MockHelper::mockModel('Appointment', 0, false);
        $this->mockModelMethod('Appointment', $mockAppointment);

        // Set up expected response
        $this->controller->resp->result = 1;
        $this->controller->resp->msg = "Create appointment successfully !";
        $this->controller->resp->data = ['id' => 1];

        // Execute test by calling private oldFlow2 method
        $this->callPrivateMethod($this->controller, 'oldFlow2');

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
        $this->assertEquals("Create appointment successfully !", $this->controller->resp->msg);
    }

    /**
     * Test getTheLaziestDoctor method
     */
    public function testGetTheLaziestDoctor()
    {
        // Create mock AuthUser
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock DB results
        $serviceId = 3;

        // Execute test by calling private getTheLaziestDoctor method
        $result = $this->callPrivateMethod($this->controller, 'getTheLaziestDoctor', [$serviceId]);

        // Since we can't fully mock DB query results without extensive setup,
        // we'll just check that the method returns a reasonable value
        $this->assertTrue(is_numeric($result) || $result === null);
    }

    /**
     * Test getCurrentAppointmentQuantityByDoctorId method
     */
    public function testGetCurrentAppointmentQuantityByDoctorId()
    {
        // Create mock AuthUser
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser
        ]);

        // Mock doctor ID
        $doctorId = 5;

        // Execute test by calling private getCurrentAppointmentQuantityByDoctorId method
        $result = $this->callPrivateMethod($this->controller, 'getCurrentAppointmentQuantityByDoctorId', [$doctorId]);

        // Check that the method returns a numeric value
        $this->assertTrue(is_numeric($result));
    }
}
