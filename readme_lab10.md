1. Middleware
- AuthMiddleware: Kiểm tra đã đăng nhập chưa, nếu chưa thì tự động đẩy về trang login.
- GuestMiddleware: Ngăn người dùng đã đăng nhập vào lại trang login, tự điều hướng về dashboard.
- CsrfMiddleware: Sinh token ẩn và kiểm tra tính hợp lệ của token khi submit form để chống giả mạo request.

2. Session và Cookie
- Session lưu dữ liệu bảo mật trên Server, còn Cookie lưu dữ liệu dạng file nhỏ ở trình duyệt máy khách.
- $_SESSION["user"] dùng để lưu thông tin người dùng đã đăng nhập trong suốt phiên làm việc.
- GET/POST chỉ truyền dữ liệu tức thời giữa 2 request, còn Session duy trì dữ liệu xuyên suốt nhiều trang.
- Server gửi Session ID về trình duyệt qua cookie PHPSESSID, ở các request sau trình duyệt tự gửi lại PHPSESSID để Server nhận diện phiên.

3. Đăng nhập, bảo mật và phân quyền
- password_verify() dùng để kiểm tra mật khẩu nhập vào có khớp với chuỗi mã hóa trong CSDL hay không.
- Bcrypt là thuật toán hash 1 chiều an toàn có sẵn muối. Cần lưu hash để bảo vệ mật khẩu gốc khi lộ CSDL. Thuật toán khác: Argon2, SHA-256.
- Kiểm tra Session trước khi vào Admin nhằm chặn người dùng chưa đăng nhập gõ trực tiếp URL vào trang quản trị.
- Khi đăng xuất cần thực hiện session_unset() xóa biến, session_destroy() hủy phiên trên Server và xóa cookie ghi nhớ.
- CSRF Token dùng để đảm bảo yêu cầu gửi lên xuất phát từ đúng form của hệ thống chứ không phải từ website độc hại.
- Nếu CSRF Token không hợp lệ, Server sẽ lập tức chặn và từ chối xử lý request.
- Authentication là xác thực bạn là ai (ví dụ nhập đúng pass để login), còn Authorization là phân quyền bạn được làm gì (ví dụ Admin có quyền xóa người dùng còn Nhân viên chỉ được xem).
