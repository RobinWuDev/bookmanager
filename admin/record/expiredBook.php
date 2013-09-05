<?php require_once('../adminHeader.html'); ?>

<?php 
require_once('../../models/book.php');
require_once('../../models/record.php');
if($_SERVER['REQUEST_METHOD']=='GET'){
} else {
    $type    = $_POST['type'];
    if (empty($type)) {
      $bookId   = $_POST['book_id'];
      $personId = $_POST['person_id'];
      $result = BookManager::renewBook($bookId,$personId);
      $showInfo = "";
      if ($result !== false) {
        $showInfo = "续借成功";
      } else {
        $showInfo = "续借失败";
      }
      echo '<script>history.back();alert("'.$showInfo.'");</script>';
    } else {
      $renewBooks = $_POST['renew'];
      $falseCount = 0;
      foreach ($renewBooks as $value) {
        $info =  split("_",$value);
        $bookId = $info[0];
        $personId = $info[1];
        $result = BookManager::renewBook($bookId,$personId);
        if ($result === false) {
          $falseCount++;
        }
      }
      $showInfo = "成功:".(count($renewBooks)-$falseCount);
      echo '<script>history.back();alert("'.$showInfo.'");</script>';
    }
    
}

$books = BookManager::expiredBooksWithRecordInfo();
if ($books === false) {
  echo "<script>history.back();alert('获取数据失败');</script>";
  return;
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
      <?php require_once "../adminLeftNav.html"; ?>

      <div class="span9" id="content">
        <h3 id="store_h3">过期图书列表</h3>
        <form method="POST" action="expiredBook.php" >
        <table class="table table-hover">
          <thead>
            <tr>
              <th></th>
              <th>编号</th>
              <th>书名</th>
              <th>状态</th>
              <th>借阅人</th>
              <th>借阅时间</th>
              <th>剩余天数</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody id="store_list">
            <?php
                foreach ($books['books'] as $book) {
                  $bookStatuStr = "";
                  if ($book->status == 0) {
                    $bookStatuStr = "在库";
                  } else if ($book->status == -1) {
                    $bookStatuStr = "丢失";
                  } else {
                    $bookStatuStr = "借出";
                  }
                  $renewJsCode = 'if (confirm(\'你确定要续借？\')) {
                                    var f = document.createElement(\'form\');
                                    f.style.display = \'none\';
                                    this.parentNode.appendChild(f);
                                    f.method = \'POST\';
                                    f.action = document.URL;
                                    var m = document.createElement(\'input\');
                                    m.setAttribute(\'type\', \'hidden\');
                                    m.setAttribute(\'name\', \'book_id\');
                                    m.setAttribute(\'value\', '.$book->bookId.');
                                    f.appendChild(m);
                                    var s = document.createElement(\'input\');
                                    s.setAttribute(\'type\', \'hidden\');
                                    s.setAttribute(\'name\', \'person_id\');
                                    s.setAttribute(\'value\', '.$book->currentRecord->personId.');
                                    f.appendChild(s);
                                    f.submit();
                                };
                                return false;';
                  $renewButtonHtml = '';
                  if ($book->status == 1) {
                    $renewButtonHtml .= '<button class="btn btn-info" onclick="'.$renewJsCode.'">续借</button>';
                  }
                  $echoStr = '<tr>'
                      .'<td><input type="checkbox" name="renew[]" value="'
                      .$book->bookId.'_'.$book->currentRecord->personId
                      .'" /></td>'
                      .'<td>'.$book->sunccoNo.'</td>'
                      .'<td><a href="/admin/book/bookDetail.php?bookId=$book->bookId">'.$book->name.'</a></td>'
                      .'<td>'.$bookStatuStr.'</td>'
                      .'<td>'.$book->currentRecord->personName.'</td>'
                      .'<td>'.$book->currentRecord->borrowTime.'</td>';
                      if ($book->expiredDays > 0) {
                             $echoStr .= '<td bgcolor="#ffff0">';
                      } else {
                             $echoStr .= '<td bgcolor="#ff0000">';
                      }
                      $echoStr .= $book->expiredDays."</td>";
                      $echoStr .= "<td>".$renewButtonHtml."</td></tr>";
                      echo $echoStr;
                }
             ?>
        </tbody>
      </table>
      <input type="hidden" name="type" value="multiple"/>
      <?php
      if (count($books['books']) != 0){
        echo '<button class="btn btn-info" type="commit">续借</button>';
      }
      ?>
      </form>
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