# Bài toán
Bài toán chuyển đổi từ nhị phân sang thập phân:
> So given a string like "100110101" , we need to write a function that spits out 309 .
# 1. Use a for loop
```php
function binaryToDecimal($binary)
{
    $total = 0;
    $exponent = strlen($binary) - 1;
    
    for ($i = 0; $i < strlen($binary); $i++) {
        $decimal = $binary[$i] * (2 ** $exponent);
        $total += $decimal;
        $exponent--;
    }
    return $total;
}
```
# 2. Breaking It Down
> Một trong những thức quan trọng tôi muốn bạn học được từ cuốn sách này là cách dừng làm quá nhiều thứ trong 1 lúc và thay vào đó giải quyết vấn đề nhỏ, những bước đơn giản

Hãy tưởng tượng trong giây lát rằng chúng ta không cho phép sử dụng biến tạm thời (trung gian). Vậy làm sao chúng ta có thể giải quyết vấn đề mà chỉ cho phép thực hiện các phép toán tử với dữ liệu đầu vào như 1 thể thống nhất?

Đầu tiên cần tách chuỗi ra đã. Bạn có thể làm việc đó bằng cách sử dụng str_split, cái mà sẽ bọc kết quả trong collection
```php
function binaryToDecimal($binary)
{
    // $binary => "11010"
    $columns = collect(str_split($binary));
    // $columns => ["1", "1", "0", "1", "0"]
}
```
OK, vậy chúng ta đã có 1 collection từ chuỗi string. Chúng ta sẽ làm gì tiếp theo để giải quyết vấn đề.

Quay trở lại vấn đề, chúng ta nói về chuyển đổi dữ liệu nhị phân sang thập phân, có vẻ như dùng map ư?
# 3. Reversing the Collection
Phải đảo ngược lại collection
```php
$columns->reverse();
// => [
// 4 => "0",
// 3 => "1",
// 2 => "0",
// 1 => "1",
// 0 => "1",
// ]
```
Sử dụng values để lấy giá trị. Hãy chú ý, reverse đảo ngược lại giá trị lại nhưng key của nó không đổi. Để reset lại key bắt đầu từ 0, bạn cần sử dụng toán tử values nữa
```php
function binaryToDecimal($binary)
{
    // $binary => "11010"
    $columns = collect(str_split($binary))
        ->reverse()
        ->values();
        // => [
        // 0 => "0",
        // 1 => "1",
        // 2 => "0",
        // 3 => "1",
        // 4 => "1",
        // ]
}
```
# 4. Mapping with Keys
OK, giờ là ta sẽ map kết hợp sum để tính kết quả nào
```php
function binaryToDecimal($binary)
{
    return collect(str_split($binary))
        ->reverse()
        ->values()
        ->map(function ($column, $exponent) {
            return $column * (2 ** $exponent);
        })->sum();
}
```
Điều tuyệt vời là sau khi tái cấu trúc code, không biến tạm thời. 

>* Vấn đề lớn nhất với biến tạm thời là chúng bắt bạn giữ toàn bộ hàm trong đầu bạn trong mọi lúc để suy nghĩ về cách thức hoạt động của hàm (kiểu giữ biến để truyền vào hàm sau và theo dõi luồng với nhau. Kiểu nó giữ giá trị của hàm nào và truyền vào đâu nữa ý)
>* Đối lập với phương án này là giải pháp đường ống (pipeline). Mỗi toán tử hoàn toàn độc lập. Bạn không cần hiểu giá trị của biến tạm thời ở dòng X để biết hàm reverse thực sự làm gì, nó chỉ phụ thuộc vào đầu ra của toán tử trước đó và không gì cả. Với tôi, nó là sự thanh lịch
# Tổng kết
Helper: **reverse(), values(), sum()**
> **Chú ý**: sử dụng một vài toán tử thay đổi thứ tự của collection, hãy chú ý xem chúng có đổi thứ tự key không. Như toán tử `reverse() `sẽ không thay đổi thứ tự key và bạn cần kết hợp với toán tử `values` để reset key lại từ 0

Ngoài ra với issue này, bạn có thể sử dụng hàm `bindec` của PHP