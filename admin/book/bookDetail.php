<?php require_once('../adminHeader.html'); ?>

<?php 
require_once('../../models/book.php');
require_once('../../models/record.php');
if($_SERVER['REQUEST_METHOD']=='GET'){
      $bookId     = $_GET['bookId'];
      
      $book = BookManager::getBook($bookId);
      if ($books === false) {
        echo "<script>history.back();alert('没有数据');</script>";
        return;
      }

      $records = RecordManager::searchRecordsWithPersonName($bookId);
}


               
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once "../adminHead.php"; ?></head>

<body>

  <?php require_once "../adminNav.html"; ?>

  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span3">
        <div class="well sidebar-nav">
          <ul class="nav nav-list" id="admin_left_categorys">
            <li class="nav-header">书籍详情</li>
            <li >id:<?php echo $book->bookId;?></li>
            <li >名称:<?php echo $book->name;?></li>
            <li >编号:<?php echo $book->sunccoNo;?></li>
            <li >ISBN:<?php echo $book->ISBN;?></li>
            <li >价格:<?php echo $book->price;?></li>
            <?php
                  $bookStatuStr = "";
                  if ($book->status == 0) {
                    $bookStatuStr = "在库";
                  } else if ($book->status == -1) {
                    $bookStatuStr = "丢失";
                  } else {
                    $bookStatuStr = "借出";
                  }
            ?>
            <li >状态:<?php echo $bookStatuStr;?></li>
            <li >借阅次数:<?php echo $book->count;?></li>
          </ul>
        </div>
      </div>
      

      <div class="span9" id="content">
        <h3 id="store_h3">借阅记录</h3>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>借阅人</th>
              <th>借阅时间</th>
              <th>返还时间</th>
            </tr>
          </thead>
          <tbody id="store_list">
            <?php
                if (count($records['records']) != 0 ){
                  foreach ($records["records"] as $record) {
                    echo "<tr>"
                        ."<td>".$record->personName."</td>"
                        ."<td>".$record->borrowTime."</td>"
                        ."<td>".$record->remandTime."</td>"
                        ."</tr>";
                  }
                }
                
             ?>
        </tbody>
      </table>
      <div id="pageNavId" class="span9 offset2" style="text-align: right;margin-bottom: 30px;"></div>
    </div>

    <script src="/static/js/external/pagenav1.1.min.js"></script>
    <?php
      echo "<script type=\"text/javascript\">"
                   ."pageNav.pageNavId=\"pageNavId\";"
                   ."pageNav.pre=\"上一页\";"
                   ."pageNav.next=\"下一页\";"
                   ."pageNav.url=\"/admin/book/bookManager.php?page={index}&pageSize=$pageSize\";"
                   ."pageNav.fn = function(p,pn){"
                   ."};"
                   ."pageNav.go($books[currentPage],$books[pageSum]);"
                  ."</script>";
  ?>
</div>
</div>
<?php require_once "../adminFooter.html"; ?></body>
</html>