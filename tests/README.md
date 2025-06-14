# Tests PHPUnit cho PTIT-Do-An-Tot-Nghiep

Đây là tài liệu hướng dẫn về việc cài đặt, thiết lập và chạy các bài kiểm tra (tests) cho dự án.

## Cấu trúc thư mục

```
tests/
├── controllers/           # Tests cho các controller
│   ├── AppointmentControllerTest.php
│   ├── AppointmentQueueControllerTest.php
│   ├── AppointmentQueueNowControllerTest.php
│   └── AppointmentsControllerTest.php
├── TestCase.php           # Class TestCase cơ sở với các phương thức tiện ích
├── MockHelper.php         # Helper để mock các đối tượng DB, Input, Model, v.v.
└── bootstrap.php          # File thiết lập môi trường và hằng số cho tests
```

## Cài đặt

1. Cài đặt PHPUnit và các dependency cần thiết:

```bash
composer install
```

## Cấu hình

Các thông số cấu hình được đặt trong file `bootstrap.php` và `phpunit.xml`:

- **bootstrap.php**: Cấu hình môi trường test, kết nối database và các hằng số
- **phpunit.xml**: Cấu hình PHPUnit, thư mục tests và các tùy chọn khác

## Chạy Tests

### Sử dụng script có sẵn:

#### Windows:

```bash
run-tests.bat
```

Nếu bạn đang sử dụng XAMPP, script này sẽ tự động tìm đường dẫn PHP của XAMPP.

#### Linux/Mac:

```bash
./run-tests.sh
```

hoặc chạy trực tiếp:

```bash
php vendor/bin/phpunit
```

### Chạy tests cụ thể:

```bash
php vendor/bin/phpunit tests/controllers/AppointmentControllerTest.php
```

### Chạy một test method cụ thể:

```bash
php vendor/bin/phpunit --filter testProcessWithGetRequest tests/controllers/AppointmentControllerTest.php
```

## Danh sách các Tests

1. **AppointmentControllerTest**

   - Tests cho CRUD operations trong AppointmentController
   - Kiểm tra quyền truy cập dựa trên vai trò
   - Tests cho các phương thức:
     - getById
     - update
     - confirm
     - delete

2. **AppointmentQueueControllerTest**

   - Tests cho quản lý queue trong AppointmentQueueController
   - Kiểm tra bộ lọc và phân quyền
   - Tests cho các phương thức:
     - getAll
     - arrange
     - oldFlowArrange
     - getQueue

3. **AppointmentQueueNowControllerTest**

   - Tests cho hiển thị queue hiện tại trong AppointmentQueueNowController
   - Tests cho các phương thức:
     - getQueue
     - Kiểm tra vai trò người dùng (member, admin, supporter)

4. **AppointmentsControllerTest**
   - Tests cho quản lý nhiều appointments trong AppointmentsController
   - Tests cho các phương thức:
     - getAll
     - oldFlow
     - oldFlow2
     - newFlow
     - getTheLaziestDoctor
     - getCurrentAppointmentQuantityByDoctorId

## Chức năng MockHelper và TestCase

### MockHelper

`MockHelper` cung cấp các phương thức để tạo mock objects cho:

- Models (mockModel)
- Database (mockDB)
- Input/Request (mockInput)
- Auth (mockAuth)
- Response (mockResponse)

```php
// Ví dụ tạo mock model:
$mockAppointment = MockHelper::mockModel('Appointment', $id, true, $data);

// Ví dụ mock database:
$mockDB = MockHelper::mockDB($results);
```

### TestCase

`TestCase` cơ sở cung cấp các phương thức tiện ích để:

- Cài đặt controller với dependencies (setupController)
- Mock HTTP method (mockInputMethod)
- Tạo mock user (createMockAuthUser)
- Tạo mock route (createMockRoute)
- Gọi private methods (callPrivateMethod)

```php
// Ví dụ gọi private method:
$result = $this->callPrivateMethod($controller, 'methodName', [$param1, $param2]);
```

## Viết thêm Tests

Để viết thêm test cho các controller khác:

1. Tạo một class test mới trong thư mục `tests/controllers/`:

```php
<?php

namespace Tests\Controllers;

use Tests\TestCase;
use Tests\MockHelper;

class NewControllerTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = new \NewController();
    }

    public function testSomeMethod()
    {
        // Thiết lập test
        $mockAuthUser = $this->createMockAuthUser('admin');
        $this->setupController($this->controller, [
            'AuthUser' => $mockAuthUser,
        ]);

        // Gọi phương thức cần test
        $result = $this->callPrivateMethod($this->controller, 'someMethod');

        // Kiểm tra kết quả
        $this->assertEquals(1, $result);
    }
}
```

2. Thêm các assertions để kiểm tra kết quả
3. Chạy tests để kiểm tra

## Lưu ý

- Các tests được thiết kế để chạy độc lập mà không ảnh hưởng đến DB thật
- Tests sử dụng mock objects để mô phỏng database, Input, và các dependencies khác
- Do giới hạn trong việc mock static methods, một số tests chỉ kiểm tra cấu trúc response
- Có thể cần sửa đổi `bootstrap.php` để phản ánh cấu hình XAMPP cụ thể của bạn
- Nếu bạn thay đổi cấu trúc database, bạn có thể cần cập nhật các mock objects để phản ánh các thay đổi

## Giải quyết vấn đề

### Lỗi thường gặp:

1. **PHPUnit không tìm thấy**:

   ```
   PHPUnit not found. Please run "composer install" first to install dependencies.
   ```

   Giải pháp: Chạy `composer install` để cài đặt PHPUnit

2. **Lỗi kết nối database**:
   Kiểm tra các thông số kết nối trong `bootstrap.php`

3. **Lỗi "Class not found"**:
   Kiểm tra autoloader và đường dẫn trong `composer.json` và `phpunit.xml`
