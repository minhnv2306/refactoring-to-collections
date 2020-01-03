Đây là 1 project tôi viết 1 vài tháng trước. Ứng dụng quản lý cuộc thi và tôi cần viết code để làm sao để tính rank các team sau khi cuộc thi kết thúc
Tôi bắt đầu nhận với collection là điểm các đội như sau
```php
$scores = collect([
    ['score' => 76, 'team' => 'A'],
    ['score' => 62, 'team' => 'B'],
    ['score' => 82, 'team' => 'C'],
    ['score' => 86, 'team' => 'D'],
    ['score' => 91, 'team' => 'E'],
    ['score' => 67, 'team' => 'F'],
    ['score' => 67, 'team' => 'G'],
    ['score' => 82, 'team' => 'H'],
]);
```
Nó có vẻ đơn giản bằng cách sử dụng method `sortByDesc`, cái mà sẽ nhận vào tên trường và sắp xếp nó như 1 tham số
```php
$rankedScores = $scores->sortByDesc('score');
// => [
// ['score' => 91, 'team' => 'E'],
// ['score' => 86, 'team' => 'D'],
// ['score' => 82, 'team' => 'C'],
// ['score' => 82, 'team' => 'H'],
// ['score' => 76, 'team' => 'A'],
// ['score' => 67, 'team' => 'F'],
// ['score' => 67, 'team' => 'G'],
// ['score' => 62, 'team' => 'B'],
// ];
```
Bây giờ chúng đã được sắp xếp, chúng ta chỉ có thể sử dụng chỉ số của mảng cộng thêm 1 như là rank, đúng ko? Không hẳn, bởi vì `sortByDesc` thực sự vẫn giữ lại `key` cũ, do vậy mặc dù `$rankedScores` đã được sắp xếp đúng thứ tự nhưng chúng thực sự , chúng vẫn có key rõ ràng không phải là ranking mong đợi
```php
$rankedScores = $scores->sortByDesc('score');
// => [
// 4 => ['score' => 91, 'team' => 'E'],
// 3 => ['score' => 86, 'team' => 'D'],
// 2 => ['score' => 82, 'team' => 'C'],
// 7 => ['score' => 82, 'team' => 'H'],
// 0 => ['score' => 76, 'team' => 'A'],
// 5 => ['score' => 67, 'team' => 'F'],
// 6 => ['score' => 67, 'team' => 'G'],
// 1 => ['score' => 62, 'team' => 'B'],
// ];
```
Một cách đơn giản để fix chỗ này là gọi phương thức `values`, nó sẽ xóa các key đã tồn tại đi và reset chúng về dạng bình thường
```php
$rankedScores = $scores->sortByDesc('score')->values();
// => [
// 0 => ['score' => 91, 'team' => 'E'],
// 1 => ['score' => 86, 'team' => 'D'],
// 2 => ['score' => 82, 'team' => 'C'],
// 3 => ['score' => 82, 'team' => 'H'],
// 4 => ['score' => 76, 'team' => 'A'],
// 5 => ['score' => 67, 'team' => 'F'],
// 6 => ['score' => 67, 'team' => 'G'],
// 7 => ['score' => 62, 'team' => 'B'],
// ];
```
Có vẻ tốt hơn 1 chút rồi nhưng ranking thực tế vẫn bị giảm đi 1 đúng không? Tôi nghĩ nó sẽ tốt hơn nếu bạn có thể thêm trường rank cho mỗi điểm để lấy rank thực sự, bắt đầu từ 1 thay vì 0.
# 1. Zipping-in the Ranks
Một cách để làm việc này là dùng `zip` điểm số với danh sách `ranks`.

Chúng ta có thể sinh danh sách các ranks bằng cách sử dụng `range($start, $end)`
```php
$rankedScores = $scores->sortByDesc('score')
    ->zip(range(1, $scores->count()));
// => [
// [['score' => 91, 'team' => 'E'], 1],
// [['score' => 86, 'team' => 'D'], 2],
// [['score' => 82, 'team' => 'C'], 3],
// [['score' => 82, 'team' => 'H'], 4],
// [['score' => 76, 'team' => 'A'], 5],
// [['score' => 67, 'team' => 'F'], 6],
// [['score' => 67, 'team' => 'G'], 7],
// [['score' => 62, 'team' => 'B'], 8],
// ];
```
Một cách tốt khi sử dụng theo hướng này là chúng ta có thể bỏ lời gọi `values`, chúng ta không phải thực sự lo về vấn đề `keys` thêm 1 lần nào nữa.

Sau khi `zip` điểm số với rank của họ, chúng ta có thể sử dụng `map` để thêm rank vào như 1 trường thực sự:
```php
$rankedScores = $scores->sortByDesc('score')
    ->zip(range(1, $scores->count()))
    ->map(function ($scoreAndRank) {
        list($score, $rank) = $scoreAndRank;
        return array_merge($score, [
            'rank' => $rank
        ]);
    });
// => [
// ['rank' => 1, 'score' => 91, 'team' => 'E'],
// ['rank' => 2, 'score' => 86, 'team' => 'D'],
// ['rank' => 3, 'score' => 82, 'team' => 'C'],
// ['rank' => 4, 'score' => 82, 'team' => 'H'],
// ['rank' => 5, 'score' => 76, 'team' => 'A'],
// ['rank' => 6, 'score' => 67, 'team' => 'F'],
// ['rank' => 7, 'score' => 67, 'team' => 'G'],
// ['rank' => 8, 'score' => 62, 'team' => 'B'],
// ];
```
# 2. Dealing with Ties (Giao dịch với điểm bằng nhau)
Nếu bạn nhìn kỹ điểm đã được sắp xếp, bạn sẽ nhận thấy có 2 tập có điểm bằng nhau
```php
    ['rank' => 3, 'score' => 82, 'team' => 'C'],
    ['rank' => 4, 'score' => 82, 'team' => 'H'],
```
Liệu có thực sự chơi đẹp không khi team C nhận vị trí thứ 3 còn team H nhận ví trí thứ 4 mặc dù 2 đội bằng điểm? Tại sao không có cách xử ý nào khác?

Cách xử lý có thể áp dụng luật xếp rank https://en.wikipedia.org/wiki/Ranking#Standard_competition_ranking_.28.221224.22_ranking.29 khi 2 đội bằng điểm, bỏ qua thứ hạng và những điểm số khác. 

Và đây là cái theo như chuẩn ranking sẽ cần điều chỉnh:
```php
[
    ['rank' => 1, 'score' => 91, 'team' => 'E'],
    ['rank' => 2, 'score' => 86, 'team' => 'D'],
    ['rank' => 3, 'score' => 82, 'team' => 'C'],
    ['rank' => 3, 'score' => 82, 'team' => 'H'],
    ['rank' => 5, 'score' => 76, 'team' => 'A'],
    ['rank' => 6, 'score' => 67, 'team' => 'F'],
    ['rank' => 6, 'score' => 67, 'team' => 'G'],
    ['rank' => 8, 'score' => 62, 'team' => 'B'],
];
```
OK, ý tưởng là vậy, nhưng làm sao chúng ta có thể thực thi chúng?
# 3. One Step at a Time
Tôi có chút thú nhận khi làm: Mất rất nhiều thời gian khi tôi giải quyết vấn đề này với `collection pipelines`, tôi không có ý tưởng gì để giải quyết vấn đề này.

Một trong những thứ tốt đẹp nhất mà `collection pipeline` mang lại là mỗi bước nhỏ  và rời rạc. Chúng ta đã nói 1 chút về nó, về cách mà nó làm cho code trở nên dễ theo dõi hơn nhưng không dễ để viết.

Thay vì tìm ra tất cả các thuật toán, tôi chỉ chưa bao giờ phải lo lắng về việc nhận 1 bước nào đó gần hơn cho vấn đề tôi đang gặp phải. Nếu có đủ thời gian, tôi đã có thể kết thúc vấn đề.

Nhưng tôi đang cố cần thực hiện bước nào để thực thi được việc sắp xếp rank theo chuẩn?
# 4. Grouping by Score
Nếu team bạn đang có các điểm giống nhau và hỗ trợ nhận các rank giống nhau, sẽ sắp xếp kết quả theo score có giải là đủ để giải quyết vấn đề:
```php
$rankedScores = $scores->sortByDesc('score')
    ->zip(range(1, $scores->count()))
    ->map(function ($scoreAndRank) {
        list($score, $rank) = $scoreAndRank;

        return array_merge($score, [
            'rank' => $rank
        ]);
    })
    ->groupBy('score');
```
Kết quả chúng ta nhận được sẽ như sau:
```php
[
    91 => [
        ['rank' => 1, 'score' => 91, 'team' => 'E']
    ],
    86 => [
        ['rank' => 2, 'score' => 86, 'team' => 'D']
    ],
    82 => [
        ['rank' => 3, 'score' => 82, 'team' => 'C'],
        ['rank' => 4, 'score' => 82, 'team' => 'H'],
    ],
    76 => [
        ['rank' => 5, 'score' => 76, 'team' => 'A']
    ],
    67 => [
        ['rank' => 6, 'score' => 67, 'team' => 'F'],
        ['rank' => 7, 'score' => 67, 'team' => 'G'],
    ],
    62 => [
        ['rank' => 8, 'score' => 62, 'team' => 'B']
    ],
];
```
# 5. Adjusting the Ranks
Như vậy chúng ta có tất cả kết quả đã được nhóm theo điểm và chúng ta muốn chắc chắn rằng 1 vài team có điểm giống nhau sẽ có rank tốt nhất có thể. Hãy nhìn xem 1 phần của các cặp điểm giống nhau và chúng ta có thể nghỉ cách nào để làm việc với nó:
```php
$tiedScores = collect([
    ['rank' => 3, 'score' => 82, 'team' => 'C'],
    ['rank' => 4, 'score' => 82, 'team' => 'H'],
]);
```
Với nhóm kết quả này, làm sao chúng ta chắc chắn rằng cả 2 đều ở vị trí thứ 3.

Đầu tiên chúng ta cần tìm ra rank tốt nhất trong nhóm. Chúng ta có thể làm điều này bằng cách sử dụng `pluck` để lấy tập collection của mỗi rank và sử dụng `min` để lấy rank nhỏ nhất của collection:
```php
$lowestRank = $tiedScores->pluck('rank')->min();
```
Đủ dễ rồi! Bây giờ chúng ta cần gán rank giống nhau với mỗi team. Chúng ta có thể sử dụng `map` cho mỗi team để chuyển đổi mỗi kết quả:
```php
$lowestRank = $tiedScores->pluck('rank')->min();

$adjustedScores = $tiedScores->map(function ($rankedScore) use ($lowestRank) {
    $rankedScore['rank'] = $lowestRank;
    return $rankedScore;
})
```
> Chúng ta có 1 chút cẩn thận vì chúng ta không được phép biến đổi trong hàm `map`, bạn nhớ chứ?

Trong trường hợp này, sự thay đổi rank `key` không biến đổi gì về mặt kỹ thuật ngoài hàm `closure` vì hàm trong PHP được pass by value nhưng nếu chúng ta làm việc với objects, nó sẽ là vấn đề lớn.
Với sự lợi ích nhất quán, chúng tôi khuyên bạn nên trả về hàm mới, sử dụng `array_merge` để thay thế rank cũ bằng 1 hàm mới:
```php
$lowestRank = $tiedScores->pluck('rank')->min();
$adjustedScores = $tiedScores->map(function ($rankedScore) use ($lowestRank) {
    return array_merge($rankedScore, [
        'rank' => $lowestRank
    ]);
})
```
Sau khi áp dụng việc chuyển đổi, chúng ra nhận được tập các điểm mới nhìn như sau:
```php
[
    ['rank' => 3, 'score' => 82, 'team' => 'C'],
    ['rank' => 3, 'score' => 82, 'team' => 'H'],
];
```
Bây giờ, cả 2 team có điểm giống nhau với vị trí thứ 3, đúng như chúng ta muốn, perfect!
Để áp dụng việc chuyển đổi cho mỗi nhóm điểm, chúng ta chỉ cần map mỗi nhóm là được.
# 6. Collapse and Sort: Thu gọn và sắp xếp
Bây giờ kết quả của chúng ta đã được sắp xếp theo điểm. Chúng ta có thể làm phẳng chúng xuống bằng cách sử dụng collapse. Kết quả thu được sẽ như sau:
```php
[
    ['rank' => 1, 'score' => 91, 'team' => 'E'],
    ['rank' => 2, 'score' => 86, 'team' => 'D'],
    ['rank' => 3, 'score' => 82, 'team' => 'C'],
    ['rank' => 3, 'score' => 82, 'team' => 'H'],
    ['rank' => 5, 'score' => 76, 'team' => 'A'],
    ['rank' => 6, 'score' => 67, 'team' => 'F'],
    ['rank' => 6, 'score' => 67, 'team' => 'G'],
    ['rank' => 8, 'score' => 62, 'team' => 'B'],
];
```
Tuyệt vời, chúng ta đã có kết quả như chúng ta mong đợi.

Sau đó chúng ta có thể sắp xếp rank theo ý.
# 7. Cleaning Up
Đây là những thứ chặt chẽ tôi sẽ đẩy vào trong hàm
```php
function rank_scores($scores)
{
    return collect($scores)
        ->sortByDesc('score')
        ->zip(range(1, $scores->count()))
        ->map(function ($scoreAndRank) {
            list($score, $rank) = $scoreAndRank;
            return array_merge($score, [
                'rank' => $rank
            ]);
        })
        ->groupBy('score')
        ->map(function ($tiedScores) {
            $lowestRank = $tiedScores->pluck('rank')->min();
            return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
                return array_merge($rankedScore, [
                    'rank' => $lowestRank
                ]);
            });
        })
        ->collapse()
        ->sortBy('rank');
}
```
Hãy nhìn chỗ code này, phần hàm map thứ 2 hoàn toàn có thể thay thế bởi 1 hàm với tên gọi `apply_min_rank`
```php
/**
function rank_scores($scores)
{
    return collect($scores)
        ->sortByDesc('score')
        ->zip(range(1, $scores->count()))
        ->map(function ($scoreAndRank) {
            list($score, $rank) = $scoreAndRank;

            return array_merge($score, [
                'rank' => $rank
            ]);
        })
        ->groupBy('score')
**/
        ->map(function ($tiedScores) {
            $lowestRank = $tiedScores->pluck('rank')->min();
                return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
                    return array_merge($rankedScore, [
                        'rank' => $lowestRank
                    ]);
                });
        })
/**
        ->collapse()
        ->sortBy('rank');
}
**/
```
Thay thế bằng:
```php
/**
function rank_scores($scores)
{
    return collect($scores)
        ->sortByDesc('score')
        ->zip(range(1, $scores->count()))
        ->map(function ($scoreAndRank) {
            list($score, $rank) = $scoreAndRank;

            return array_merge($score, [
                'rank' => $rank
            ]);
        })
        ->groupBy('score')
**/
        ->map(function ($tiedScores) {
            return apply_min_rank($tiedScores);
        })
/**
        ->collapse()
        ->sortBy('rank');
}
**/
function apply_min_rank($tiedScores)
{
    $lowestRank = $tiedScores->pluck('rank')->min();
    return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
        return array_merge($rankedScore, [
            'rank' => $lowestRank
        ]);
    });
}
```
Nhìn có vẻ đẹp hơn 1 chút xíu, Nhưng chúng chưa thực sự giàu tính biểu đạt.
# 8. Grouping Operations
Phần map ở trên được sử dụng để **gán và khởi tạo rank** cho mỗi điểm.
```php
function rank_scores($scores)
{
    return collect($scores)
        ->sortByDesc('score')
        ->zip(range(1, $scores->count()))
        ->map(function ($scoreAndRank) {
            list($score, $rank) = $scoreAndRank;

            return array_merge($score, [
                'rank' => $rank
            ]);
        })
/**
        ->groupBy('score')
        ->map(function ($tiedScores) {
            $lowestRank = $tiedScores->pluck('rank')->min();
                return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
                    return array_merge($rankedScore, [
                        'rank' => $lowestRank
                    ]);
                });
        })
        ->collapse()
        ->sortBy('rank');
}
**/
```
Tương tự 3 bước ở dưới đang **chỉnh sửa rank có cùng điểm số.** 
```php
/**
function rank_scores($scores)
{
    return collect($scores)
        ->sortByDesc('score')
        ->zip(range(1, $scores->count()))
        ->map(function ($scoreAndRank) {
            list($score, $rank) = $scoreAndRank;

            return array_merge($score, [
                'rank' => $rank
            ]);
        })
**/
        ->groupBy('score')
        ->map(function ($tiedScores) {
            $lowestRank = $tiedScores->pluck('rank')->min();
                return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
                    return array_merge($rankedScore, [
                        'rank' => $lowestRank
                    ]);
                });
        })
        ->collapse()
        ->sortBy('rank');
}
```
Chúng ta có thể xử chúng không nhỉ?
# 9. Breaking the Chain
Như đã nói ở trên, hàm `assign_initial_rankings` sẽ có dạng như sau:
```php
function assign_initial_rankings($scores)
{
    return $scores->sortByDesc('score')
        ->zip(range(1, $scores->count()))
        ->map(function ($scoreAndRank) {
            list($score, $rank) = $scoreAndRank;
            return array_merge($score, [
                'rank' => $rank
            ]);
        });
}
```
Và hàm `adjust_rankings_for_ties`:
```php
function adjust_rankings_for_ties($scores)
{
    return $scores->groupBy('score')
        ->map(function ($tiedScores) {
            return apply_min_rank($tiedScores);
        })
        ->collapse();
}
```
Cả 2 đều đơn giản và dễ dàng để hiểu khi chúng ta tách ta nhưng chúng ta sẽ kết hợp chúng vào pipeline như thế nào? Hóa ra chúng là không thể, chúng ta phải ngắt `pipeline` và sử dụng biến tạm thời
```php
function rank_scores($scores)
{
    $rankedScores = assign_initial_rankings(collect($scores));
    $adjustedScores = adjust_rankings_for_ties($rankedScores);

    return $adjustedScores->sortBy('rank');
}
```
Nhìn hơi tù nhỉ. Chúng ta có thể thêm những phần này như là các `method` vào `collection` của chúng ta bằng cách sử dụng `macros` nhưng chúng không thực sự có ý nghĩa như csac phương thức của collection. Cả 2 đều thực hiện các mục đích khác nhau và chúng không thực sự thuộc về mục đích chung của các toán tử collection

Mất một khoảng thời gian dài, tôi chỉ chấp nhận những hạn chế này và thoát nó ra khỏi `pipeline` khi tôi cần, nhưng gần đây tôi đã vấp ngã trong 1 tiêu chuẩn cái mà làm cho tôi làm tốt cả 2 vấn đề này.
# 10. The Pipe Macro
Chúng ta sẽ có `macro` đơn giản như sau:
```php
Collection::macro('pipe', function ($callback) {
    return $callback($this);
});
```
Tất cả nó làm là gọi phương thức `pipe` cái mà nhận vào 1 callback, truyền collection vào callack và trả lại kết quả.

Khi đó code sẽ đơn giản như sau:
```php
function rank_scores($scores)
{
    return collect($scores)
        ->pipe(function ($scores) {
            return assign_initial_rankings($scores);
        })
        ->pipe(function ($rankedScores) {
            return adjust_rankings_for_ties($rankedScores);
        })
        ->sortBy('rank');
}
```
Như vậy chúng ta lại nhận được 1 pipeline! 
> PHP cho phép ta đối xử với string như là 1 callback nếu nó trùng với tên function

Do vậy chúng ta thậm chí cho thể viết:
```php
function rank_scores($scores)
{
    return collect($scores)
        ->pipe('assign_initial_rankings')
        ->pipe('adjust_rankings_for_ties')
        ->sortBy('rank');
}
```
Không thể có nhiều biểu cảm hơn nữa.

Và đây là luồng chung của cả công việc phân rank:
```php
function rank_scores($scores)
{
    return collect($scores)
        ->pipe('assign_initial_rankings')
        ->pipe('adjust_rankings_for_ties')
        ->sortBy('rank');
}
function assign_initial_rankings($scores)
{
    return $scores->sortByDesc('score')
        ->zip(range(1, $scores->count()))
        ->map(function ($scoreAndRank) {
            list($score, $rank) = $scoreAndRank;
            return array_merge($score, [
                'rank' => $rank
            ]);
        });
}
function adjust_rankings_for_ties($scores)
{
    return $scores->groupBy('score')->map(function ($tiedScores) {
        return apply_min_rank($tiedScores);
    })->collapse();
}
function apply_min_rank($tiedScores)
{
    $lowestRank = $tiedScores->pluck('rank')->min();
    return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
        return array_merge($rankedScore, [
            'rank' => $lowestRank
        ]);
    });
}
```
Thật không tệ với một vấn đề phức tạp phải không nào!!!