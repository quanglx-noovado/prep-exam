# Authentication Service

Hệ thống xác thực đa thiết bị với OTP.

## Cài đặt

1. Clone project và cài đặt môi trường:
```bash
git clone <repository_url>
cp .env.example .env
```

2. Cấu hình file .env:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=prep_database
DB_USERNAME=quang
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=<mail_host>
MAIL_PORT=<mail_port>
MAIL_USERNAME=<mail_username>
MAIL_PASSWORD=<mail_password>
```

3. Khởi động Docker:
```bash
docker-compose up -d
```

4. Cài đặt và khởi tạo ứng dụng:
```bash
docker exec -it prep_app bash
composer install
php artisan key:generate
php artisan migrate
php artisan passport:install
```

5. Đầu api sẽ chạy trên http://localhost:8000

## Luồng xử lý chính

### 1. Đăng nhập
- **API**: `POST /api/v1/login`
- **Mô tả**: Xác thực người dùng và kiểm tra thiết bị
- **Các trường hợp xử lý**:
  1. Đăng nhập thành công → Trả về token
  2. Thiết bị mới → Yêu cầu xác thực OTP
  3. Quá 3 thiết bị → Yêu cầu xóa thiết bị cũ
  4. Sai thông tin → Báo lỗi

### 2. Gửi OTP
- **API**: `POST /api/v1/send-otp`
- **Giới hạn**:
  - Gửi tối đa 5 OTP/giờ
  - OTP hết hạn sau 5 phút
### 3. Xác thực thiết bị mới
- **API**: `POST /api/v1/verify-new-device`
- **Giới hạn**: `POST /api/v1/verify-new-device`
    - Nhập sai không quá 5 lần
    - Nếu nhập sai 5 lân sẽ bị block 30'

### 4. Xác thực viêc inactive thiết bị
- **API**: `POST /api/v1/verify-remove-device`
- **Giới hạn**: `POST /api/v1/verify-new-device`
    - Nhập sai không quá 5 lần
    - Nếu nhập sai 5 lân sẽ bị block 30'

### 3. Quản lý thiết bị
- **API**: `GET /api/v1/active-devices`
