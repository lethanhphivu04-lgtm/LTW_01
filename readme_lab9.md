1. $limit dùng để quy định số bản ghi hiển thị trên mỗi trang, $page là trang hiện tại đang xem, còn $offset là số dòng cần bỏ qua từ đầu danh sách được tính bằng công thức ($page - 1) * $limit.

2. Cần dùng hàm ceil() để làm tròn lên số nguyên gần nhất, đảm bảo khi tổng số bản ghi chia cho $limit ra số lẻ thì vẫn tạo đủ trang để chứa các bản ghi còn dư ở trang cuối.

3. Trong SQL, câu lệnh LIMIT n dùng để giới hạn lấy ra tối đa n bản ghi, còn OFFSET k dùng để bỏ qua k bản ghi đầu tiên trước khi lấy dữ liệu.

4. Phải giữ tham số limit trên URL khi chuyển trang để duy trì số lượng hiển thị mỗi trang theo đúng lựa chọn của người dùng mà không bị quay về mặc định.

5. Phải giữ tham số keyword trên URL khi chuyển trang để đảm bảo dữ liệu hiển thị ở trang tiếp theo vẫn thuộc tập kết quả được lọc theo từ khóa đó.

6. Hàm count() dùng để đếm tổng số bản ghi thỏa mãn điều kiện trong CSDL, từ đó làm căn cứ tính ra tổng số trang $totalPages.

7. Tái sử dụng getPage() giúp tránh trùng lặp mã nguồn bằng cách gán giá trị mặc định cho $keyword là rỗng, giúp hàm dùng chung được cho cả lấy tất cả lẫn tìm kiếm.

8. Khi tìm kiếm không có kết quả phù hợp, tổng số bản ghi bằng 0 nên $totalPages sẽ có giá trị bằng 0.

9. Tham số sort dùng để xác định tiêu chí và thứ tự sắp xếp dữ liệu thông qua mệnh đề ORDER BY trong câu lệnh SQL.

10. Khi kết hợp tìm kiếm, sắp xếp và phân trang, cần giữ đầy đủ 4 tham số trên URL bao gồm keyword, sort, limit và page.
