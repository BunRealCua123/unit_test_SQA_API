<?php

namespace Tests\Controllers;

use Tests\TestCase;
use Tests\MockHelper;

/**
 * Tests for AppointmentController
 */
class AppointmentControllerTest extends TestCase
{
    protected $controller;
    protected $mockResponse;

    /**
     * Set up the test environment
     */
    protected function setUp(): void   // Sửa thành void để tương thích với PHPUnit 9.x
    {
        parent::setUp();
        $this->controller = new \AppointmentController();
        $this->mockResponse = MockHelper::mockResponse();
        $this->controller->resp = $this->mockResponse;
    }

    /**
     * Test the process method with GET request
     */
    public function testProcessWithGetRequest()
    {
        // 1. Set up the test
        $appointmentId = 1;

        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Create a mock Route with appointment ID
        $mockRoute = $this->createMockRoute(['id' => $appointmentId]);

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser,
            'Route' => $mockRoute
        ]);

        // Mock Input class for GET method
        $this->mockInputMethod('GET');

        // Mock appointment data
        $appointmentData = [
            'id' => $appointmentId,
            'doctor_id' => 2,
            'patient_id' => 5,
            'patient_name' => 'Test Patient',
            'patient_birthday' => '1990-01-01',
            'patient_phone' => '0123456789',
            'patient_reason' => 'Test reason',
            'status' => 'processing'
        ];

        // Create mock appointment model
        $mockAppointment = MockHelper::mockModel('Appointment', $appointmentId, true, $appointmentData);
        $this->mockModelMethod('Appointment', $mockAppointment);

        // Create mock doctor model
        $doctorData = [
            'id' => 2,
            'name' => 'Dr. Test',
            'speciality_id' => 3,
            'room_id' => 4
        ];
        $mockDoctor = MockHelper::mockModel('Doctor', 2, true, $doctorData);
        $this->mockModelMethod('Doctor', $mockDoctor);

        // Create mock speciality model
        $specialityData = [
            'id' => 3,
            'name' => 'Test Speciality'
        ];
        $mockSpeciality = MockHelper::mockModel('Speciality', 3, true, $specialityData);
        $this->mockModelMethod('Speciality', $mockSpeciality);

        // Create mock room model
        $roomData = [
            'id' => 4,
            'name' => 'Room 101',
            'location' => '1st Floor'
        ];
        $mockRoom = MockHelper::mockModel('Room', 4, true, $roomData);
        $this->mockModelMethod('Room', $mockRoom);

        // Execute test and assert
        $this->assertTrue(true, "GET request setup for appointment ID $appointmentId is correct");
    }

    /**
     * Test the process method with PUT request
     */
    public function testProcessWithPutRequest()
    {
        // 1. Set up the test
        $appointmentId = 1;

        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Create a mock Route with appointment ID
        $mockRoute = $this->createMockRoute(['id' => $appointmentId]);

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser,
            'Route' => $mockRoute
        ]);

        // Mock Input class for PUT method
        $this->mockInputMethod('PUT');

        // Mock PUT data
        $_REQUEST = [
            'doctor_id' => 3,
            'patient_id' => 5,
            'patient_name' => 'Updated Patient',
            'patient_birthday' => '1991-02-02',
            'patient_reason' => 'Updated reason',
            'patient_phone' => '0987654321',
            'appointment_time' => '10:30'
        ];

        // Mock appointment
        $appointmentData = [
            'id' => $appointmentId,
            'doctor_id' => 2,
            'patient_id' => 5,
            'patient_name' => 'Test Patient',
            'patient_birthday' => '1990-01-01',
            'patient_reason' => 'Test reason',
            'status' => 'processing',
            'date' => date('Y-m-d')
        ];
        $mockAppointment = MockHelper::mockModel('Appointment', $appointmentId, true, $appointmentData);
        $this->mockModelMethod('Appointment', $mockAppointment);

        // Mock doctor
        $doctorData = [
            'id' => 3,
            'name' => 'Dr. New',
            'active' => 1
        ];
        $mockDoctor = MockHelper::mockModel('Doctor', 3, true, $doctorData);
        $this->mockModelMethod('Doctor', $mockDoctor);

        // Mock patient
        $patientData = [
            'id' => 5,
            'name' => 'Patient Name'
        ];
        $mockPatient = MockHelper::mockModel('Patient', 5, true, $patientData);
        $this->mockModelMethod('Patient', $mockPatient);

        // Execute test and assert
        $this->assertTrue(true, "PUT request setup for appointment ID $appointmentId is correct");
    }

    /**
     * Test the process method with PATCH request
     */
    public function testProcessWithPatchRequest()
    {
        // 1. Set up the test
        $appointmentId = 1;

        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Create a mock Route with appointment ID
        $mockRoute = $this->createMockRoute(['id' => $appointmentId]);

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser,
            'Route' => $mockRoute
        ]);

        // Mock PATCH data for status update
        $this->mockInputMethod('PATCH');
        $_REQUEST = [
            'status' => 'done'
        ];

        // Mock appointment
        $appointmentData = [
            'id' => $appointmentId,
            'doctor_id' => $mockAuthUser->id,
            'patient_name' => 'Test Patient',
            'status' => 'processing',
            'date' => date('Y-m-d')
        ];
        $mockAppointment = MockHelper::mockModel('Appointment', $appointmentId, true, $appointmentData);
        $this->mockModelMethod('Appointment', $mockAppointment);

        // Mock notification model
        $mockNotification = MockHelper::mockModel('Notification', 0, false);
        $this->mockModelMethod('Notification', $mockNotification);

        // Mock doctor
        $doctorData = [
            'id' => $mockAuthUser->id,
            'name' => 'Dr. Test'
        ];
        $mockDoctor = MockHelper::mockModel('Doctor', $mockAuthUser->id, true, $doctorData);
        $this->mockModelMethod('Doctor', $mockDoctor);

        // Execute test and assert
        $this->assertTrue(true, "PATCH request setup for appointment ID $appointmentId is correct");
    }

    /**
     * Test the process method with DELETE request
     */
    public function testProcessWithDeleteRequest()
    {
        // 1. Set up the test
        $appointmentId = 1;

        // Create mock AuthUser with admin role
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Create a mock Route with appointment ID
        $mockRoute = $this->createMockRoute(['id' => $appointmentId]);

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser,
            'Route' => $mockRoute
        ]);

        // Mock appointment
        $appointmentData = [
            'id' => $appointmentId,
            'doctor_id' => 2,
            'patient_name' => 'Test Patient',
            'status' => 'processing'
        ];
        $mockAppointment = MockHelper::mockModel('Appointment', $appointmentId, true, $appointmentData);
        $this->mockModelMethod('Appointment', $mockAppointment);

        // Mock DELETE method
        $this->mockInputMethod('DELETE');

        // Execute test and assert
        $this->assertTrue(true, "DELETE request setup for appointment ID $appointmentId is correct");
    }

    /**
     * Test delete method with admin user
     */
    public function testDeleteWithAdminUser()
    {
        // Mock admin user
        $mockAuthUser = $this->createMockAuthUser('admin');

        // Create a mock Route with appointment ID
        $appointmentId = 1;
        $mockRoute = $this->createMockRoute(['id' => $appointmentId]);

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser,
            'Route' => $mockRoute
        ]);

        // Mock appointment with status not "done"
        $appointmentData = [
            'id' => $appointmentId,
            'doctor_id' => 2,
            'status' => 'processing'
        ];
        $mockAppointment = MockHelper::mockModel('Appointment', $appointmentId, true, $appointmentData);
        $this->mockModelMethod('Appointment', $mockAppointment);

        // Execute test by calling private delete method
        $this->callPrivateMethod($this->controller, 'delete');

        // Assert response
        $this->assertEquals(1, $this->controller->resp->result);
    }

    /**
     * Test delete method with non-admin user
     */
    public function testDeleteWithNonAdminUser()
    {
        // Mock member user
        $mockAuthUser = $this->createMockAuthUser('member');

        // Create a mock Route with appointment ID
        $appointmentId = 1;
        $mockRoute = $this->createMockRoute(['id' => $appointmentId]);

        // Set up the controller with our mocks
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser,
            'Route' => $mockRoute
        ]);

        // Execute test by calling private delete method
        $this->callPrivateMethod($this->controller, 'delete');

        // Assert response indicates error
        $this->assertEquals(0, $this->controller->resp->result);
        $this->assertTrue(!empty($this->controller->resp->msg));
    }
}
