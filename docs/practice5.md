
Tôi có 1 danh sách nhân viên và bạn cần chuyển đổi nó vào bảng tra cứu mapping địa chỉ email với tên của họ.
<!--more-->
Mảng đầu vào
```php
$employees = [
    [
        'name' => 'John',
        'department' => 'Sales',
        'email' => 'john@example.com'
    ],
    [
        'name' => 'Jane',
        'department' => 'Marketing',
        'email' => 'jane@example.com'
    ],
    [
        'name' => 'Dave',
        'department' => 'Marketing',
        'email' => 'dave@example.com'
    ],
];
```
Và chúng tôi cần tạo 1 mảng tra cứu có dạng như sau:
```php
$emailLookup = [
    'john@example.com' => 'John',
    'jane@example.com' => 'Jane',
    'dave@example.com' => 'Dave',
];
```
Vậy chúng ta có thể sử dụng collections và higher order functions?
# 1. Map to nowhere
Khi mà chuyển đổi dữ liệu ta nghĩ ngay đến `map`
```php
$emailLookup = $employees->map(function ($employee) {
    return $employee['name'];
});
```
Điều này sẽ cho bạn 1 danh sách tên những nhân viên, mà khoan, key đâu??
# 2. PHP's Array Identity Crisis: Cuộc khủng hoảng bản sắc
Vấn đề tùy chỉnh keys trong khi sử dụng toán tử `map` là điều mà tôi nhận được khá thường xuyên.

Tôi nghĩ lý do mà làm cho vấn đế này trở nên hóc búa là bởi vì PHP, chúng ta sử dụng kiểu dữ liệu giống nhau đại diện cho cả list và dictionary.

Quên PHP trong 1 vài phút và giả như chúng ta đang cố giải quyết vấn đề với Javascript
... (xem ví dụ về Object)

Vì vậy chúng ta không thực sự chuyển đổi 1 mảng thành mảng khác, chúng ta đang giảm (reducing) mảng ban đầu thành 1 đối tượng đơn.

Thực thi chuyển đổi trong Javascript với reduce sẽ có dạng như sau:
```php
const emailLookup = employees.reduce(function (emailLookup, employee) {
    emailLookup[employee.email] = employee.name;
    return emailLookup;
}, {});
```
Và Laravel cũng tương tự:
```php
$emailLookup = $employees->reduce(function ($emailLookup, $employee) {
    $emailLookup[$employee['email']] = $employee['name'];

    return $emailLookup;
}, []);
```
# 3. A Reusable Abstraction: Một trừu tượng hóa có thể sử dụng lại
Mặc dù chúng ta đã giải quyết được vấn đề ban đầu nhưng tôi thực sự chưa hài lòng với việc đọc code như thế nào?

Như tôi đã nhắc ở phần trước cuốn sách, tôi thường xuyên nhìn thấy `reduce` như 1 dấu hiệu rằng tôi đang quên mất cách diễn đạt dễ hình dung hơn được xây dựng với `reduce`?

=> Tức là xây dựng function cao hơn, giàu tính biểu đạt hơn dựa vào `reduce`.

Thật tuyệt vời nếu chúng ta tạo 1 toán tử với tên `toAssoc()` sử dụng để chuyển 1 danh sách vào mảng kết hợp (associative array), nhưng làm sao chúng ta có thể xác định được cả key và value?
# 4. Learning from Other Languages
Ruby có kiểu mảng kết hợp là Hash.

Bạn có thể chuyển 1 Enumerable vào trong Hash bằng cách sử dụng phương thức `to_h` miễn là enumerable tạo thành cặp `[key, value]`
```php
employees = [
    ['john@example.com', 'John'],
    ['jane@example.com', 'Jane'],
    ['dave@example.com', 'Dave'],
]
```
và gọi phương thức to_h
```php
employees.to_h
    # => {
    # 'john@example.com' => 'John',
    # 'jane@example.com' => 'Jane',
    # 'dave@example.com' => 'Dave',
# }
```
# 5. The `toAssoc` Macro
```php
Collection::macro('toAssoc', function () {
    return $this->reduce(function ($assoc, $keyValuePair) {
        list($key, $value) = $keyValuePair;
        $assoc[$key] = $value;
        return $assoc;
    }, new static);
});
```
Và có thể sử dụng
```php
$emailLookup = collect([
    ['john@example.com', 'John'],
    ['jane@example.com', 'Jane'],
    ['dave@example.com', 'Dave'],
])->toAssoc();
// => [
// 'john@example.com' => 'John',
// 'jane@example.com' => 'Jane',
// 'dave@example.com' => 'Dave',
// ]
```
# 6. Mapping to Pairs
Tất nhiên là sử dụng map, và khi đặt mọi thứ cùng nhau, nó sẽ như sau :D
```php
$emailLookup = collect([
    [
        'name' => 'John',
        'department' => 'Sales',
        'email' => 'john@example.com'
    ],
    [
        'name' => 'Jane',
        'department' => 'Marketing',
        'email' => 'jane@example.com'
    ],
    [
        'name' => 'Dave',
        'department' => 'Marketing',
        'email' => 'dave@example.com'
    ],
])->map(function ($employee) {
    return [$employee['email'], $employee['name']];
})->toAssoc();
```
Nếu bạn muốn cải tiến thêm 1 bước nữa, bạn hoàn toàn có thể xây dựng một phương thức với tên `mapToAssoc` giúp chúng ta chuyển đổi dữ liệu trong 1 bước:
```php
Collection::macro('mapToAssoc', function ($callback) {
    return $this->map($callback)->toAssoc();
});
```
Và chúng ta có thể sử dụng nó như sau:
```php
$emailLookup = $employees->mapToAssoc(function ($employee) {
    return [$employee['email'], $employee['name']];
});
```
Pretty slick!