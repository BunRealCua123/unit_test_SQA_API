<?php

use PHPUnit\Framework\TestCase;
use Pixie\Connection;
use Pixie\QueryBuilder\QueryBuilderHandler;

require_once __DIR__ . '/../../api/app/core/DataEntry.php';
require_once __DIR__ . '/../../api/app/models/AppointmentModel.php';
require_once __DIR__ . '/../../api/app/config/db.config.php';

class AppointmentModelTest extends TestCase
{
    protected static $db;
    protected static $qb;

    public static function setUpBeforeClass(): void
    {
        // Khởi tạo Pixie Connection
        $config =  require __DIR__ . '/../../LocalConfigDB.php';
        self::$db = new Connection('mysql', $config, 'DB');
        self::$qb = self::$db->getQueryBuilder();
    }
    public function setUp(): void
    {
        // Bắt đầu transaction trước mỗi test case
        self::$db->getPdoInstance()->beginTransaction();
    }

    public function tearDown()
    {
        // Rollback transaction sau mỗi test case
        self::$db->getPdoInstance()->rollback();
    }


    // Test case lấy thông tin booking từ database với một id tồn tại
    public function test_M08_AppointmentModel_select_01()
    {
        $ids = 354;
        // Arrange
        $booking = new AppointmentModel($ids);

        $this->assertTrue($booking->isAvailable());
        $this->assertEquals("354", $booking->get("id"));
        $this->assertEquals("0123456789", $booking->get("patient_phone"));
        $this->assertEquals("7", $booking->get("doctor_id"));
        $this->assertEquals("1", $booking->get("patient_id"));
        $this->assertEquals("Tiểu Long Nữ", $booking->get("patient_name"));
        $this->assertEquals("2023-02-15 20:19:03", $booking->get("update_at"));
        $this->assertEquals("2023-02-15 20:19:03", $booking->get("create_at"));

        // Verify in DB
        $dbBooking = DB::table(TABLE_PREFIX . TABLE_APPOINTMENTS)->where("id", "=", $ids)->get();
        $this->assertEquals(1, count($dbBooking));
        $this->assertEquals($dbBooking[0]->id, $booking->get("id"));
        $this->assertEquals($dbBooking[0]->patient_phone, $booking->get("patient_phone"));
        $this->assertEquals($dbBooking[0]->patient_name, $booking->get("patient_name"));
    }

    // Test case lấy thông tin booking từ database với một id không có trong db
    public function test_M08_AppointmentModel_select_02()
    {
        $ids = 999;
        // Arrange
        $booking = new AppointmentModel($ids);

        $this->assertFalse($booking->isAvailable());
        $this->assertNull($booking->get("id"));
    }

    // Test case lấy thông tin booking từ database id không hợp lệ
    public function test_M08_AppointmentModel_select_03()
    {
        $ids = "fdsfbdndassdassg";
        // Arrange
        $booking = new AppointmentModel($ids);

        $this->assertFalse($booking->isAvailable());
        $this->assertNull($booking->get("id"));
    }

    // Test case lấy thông tin booking từ database với id truyền vào là mã sql injection
    public function test_M08_AppointmentModel_select_04()
    {
        $ids = "' OR '1'='1' --";
        // Arrange
        $booking = new AppointmentModel($ids);
        $this->assertFalse($booking->isAvailable());
        $this->assertNull($booking->get("id"));
    }


    //Test case insert thành công với các trường hợp hợp lệ
    public function test_M08_AppointmentModel_insert_01()
    {
        // Arrange
        $booking = new AppointmentModel();

        $defaults = array(
            "patient_id" => "4",
            "booking_id" => "88",
            "doctor_id" => "3",
            "patient_name" => "Kiều Phong",
            "patient_birthday" => "2022-12-06",
            "patient_reason" => "code kho vai",
            "patient_phone" => "0794104124",
            "numerical_order" => "1",
            "position" => "2",
            "appointment_time" => "2025-03-17 19:00",
            "date" => "2025-03-17",
            "status" => "processing",
            "create_at" => "2023-02-15 20:19:03",
            "update_at" => "2023-02-15 20:19:03"
        );


        foreach ($defaults as $field => $value) {
            $booking->set($field, $value);
        }

        $this->assertFalse($booking->isAvailable());

        $booking->save();
        // Assert
        $this->assertTrue($booking->isAvailable());
        $this->assertNotNull($booking->get("id"));
        // Verify in DB
        $fetchBooking = new AppointmentModel($booking->get("id"));
        $this->assertEquals($booking->get("id"), $fetchBooking->get("id"));
        $this->assertEquals($booking->get("patient_id"), $fetchBooking->get("patient_id"));
        $this->assertEquals($booking->get("booking_id"), $fetchBooking->get("booking_id"));
    }

    // Test case insert không thành công với các trường null
    public function test_M08_AppointmentModel_insert_02()
    {
        $this->expectException(PDOException::class);
        // Arrange
        $booking = new AppointmentModel();

        $defaults = array(
            "patient_id" => "4",
            "booking_id" => "88",
            "doctor_id" => "3",
            "patient_name" => "Kiều Phong",
            "patient_birthday" => "2022-12-06",
            "patient_reason" => "code kho vai",
            "patient_phone" => "0794104124",
            "numerical_order" => "1",
            "position" => "2",
            "appointment_time" => null,
            "date" => null,
            "status" => null,
            "create_at" => null,
            "update_at" => null
        );


        foreach ($defaults as $field => $value) {
            $booking->set($field, $value);
        }

        $this->assertFalse($booking->isAvailable());

        $booking->save();
        // Assert
        $this->assertFalse($booking->isAvailable());
    }

    // Test case insert không thành công với patient_id không có trong db
    public function test_M08_AppointmentModel_insert_03()
    {
        $this->expectException(PDOException::class);
        // Arrange
        $booking = new AppointmentModel();

        $defaults = array(
            "patient_id" => "99999",    // patient_id không có trong db
            "booking_id" => "88",
            "doctor_id" => "3",
            "patient_name" => "Kiều Phong",
            "patient_birthday" => "2022-12-06",
            "patient_reason" => "code kho vai",
            "patient_phone" => "0794104124",
            "numerical_order" => "1",
            "position" => "2",
            "appointment_time" => "2025-03-17 19:00",
            "date" => "2025-03-17",
            "status" => "processing",
            "create_at" => "2023-02-15 20:19:03",
            "update_at" => "2023-02-15 20:19:03"
        );


        foreach ($defaults as $field => $value) {
            $booking->set($field, $value);
        }

        $this->assertFalse($booking->isAvailable());

        $booking->save();
        // Assert
        $this->assertFalse($booking->isAvailable());
    }

    // Test case insert không thành công với booking_id không có trong db
    public function test_M08_AppointmentModel_insert_04()
    {
        $this->expectException(PDOException::class);
        // Arrange
        $booking = new AppointmentModel();

        $defaults = array(
            "patient_id" => "4",
            "booking_id" => "888888888",        // booking_id không có trong db
            "doctor_id" => "3",
            "patient_name" => "Kiều Phong",
            "patient_birthday" => "2022-12-06",
            "patient_reason" => "code kho vai",
            "patient_phone" => "0794104124",
            "numerical_order" => "1",
            "position" => "2",
            "appointment_time" => "2025-03-17 19:00",
            "date" => "2025-03-17",
            "status" => "processing",
            "create_at" => "2023-02-15 20:19:03",
            "update_at" => "2023-02-15 20:19:03"
        );


        foreach ($defaults as $field => $value) {
            $booking->set($field, $value);
        }

        $this->assertFalse($booking->isAvailable());

        $booking->save();
        // Assert
        $this->assertFalse($booking->isAvailable());
    }

    // Test case insert không thành công với doctor_id null
    public function test_M08_AppointmentModel_insert_05()
    {
        $this->expectException(PDOException::class);
        // Arrange
        $booking = new AppointmentModel();

        $defaults = array(
            "patient_id" => "4",
            "booking_id" => "88",
            "patient_name" => "Kiều Phong",
            "patient_birthday" => "2022-12-06",
            "patient_reason" => "code kho vai",
            "patient_phone" => "0794104124",
            "numerical_order" => "1",
            "position" => "2",
            "appointment_time" => "2025-03-17 19:00",
            "date" => "2025-03-17",
            "status" => "processing",
            "create_at" => "2023-02-15 20:19:03",
            "update_at" => "2023-02-15 20:19:03"
        );


        foreach ($defaults as $field => $value) {
            $booking->set($field, $value);
        }

        $this->assertFalse($booking->isAvailable());

        $booking->save();
        // Assert
        $this->assertFalse($booking->isAvailable());
    }




    // Kiểm tra nhánh returnn false được thực thi hay không
    public function test_M08_AppointmentModel_update_01()
    {
        $booking = new AppointmentModel();
        $res = $booking->update();
        $this->assertFalse($res);
    }


    //Mục tiêu: kiểm tra nhánh isAvailable == true, và kiểm tra kết quả return là $this
    public function test_M08_AppointmentModel_update_02()
    {

        $booking = new AppointmentModel(354);

        $booking->set("patient_name", "Nguyen Tran Dat");

        $booking->update();

        $this->assertTrue($booking->isAvailable());
        $this->assertEquals("Nguyen Tran Dat", $booking->get("patient_name"));
        $this->assertEquals(354, $booking->get("id"));

        $getBooking = DB::table(TABLE_PREFIX . TABLE_APPOINTMENTS)->where("id", "=", 354)->get();
        $this->assertEquals(1, count($getBooking));
        $this->assertEquals(354, $getBooking[0]->id);
        $this->assertEquals("Nguyen Tran Dat", $getBooking[0]->patient_name);
    }


    // Kiểm tra nhánh returnn false được thực thi hay không
    public function test_M08_AppointmentModel_delete_01()
    {
        $booking = new AppointmentModel();
        $res = $booking->delete();
        $this->assertFalse($res);
    }


    //Mục tiêu: kiểm tra nhánh isAvailable == true, và kiểm tra kết quả return là $this
    public function test_M08_AppointmentModel_delete_02()
    {

        $booking = new AppointmentModel(354);

        $res = $booking->delete();
        $this->assertTrue($res);
        $this->assertFalse($booking->isAvailable());

        // $this->expectException(PDOException::class);
        $getBooking = DB::table(TABLE_PREFIX . TABLE_APPOINTMENTS)->where("id", "=", 354)->get();
        $this->assertEquals(0, count($getBooking));
    }
}
