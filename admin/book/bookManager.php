<?php require_once('../adminHeader.html'); ?>

<?php 
require_once('../../models/book.php');
require_once('../../models/record.php');
function showData($page,$pageSize=10,$key="") {
      if (empty($page)) {
        $page = 1;
      }
      if (empty($pageSize)) {
        $pageSize = 10;
      }

      if (empty($key)) {
        $key = "";
      }
      $books = BookManager::searchBooksWithRecordInfo($key,$page,$pageSize);
      if ($books === false || count($books["books"]) == 0) {
        echo "<script>history.back();alert('没有数据');</script>";
        return;
      }
      return $books;
}
if($_SERVER['REQUEST_METHOD']=='GET'){
      $page     = $_GET['page'];
      $pageSize = $_GET['pageSize'];
      $key      = $_GET['searchKey'];
      $books = showData($page,$pageSize,$key);
} else {
    $bookId   = $_POST['book_id'];
    $personId = $_POST['person_id'];
    $page     = $_GET['page'];
    $pageSize = $_GET['pageSize'];
    $key      = $_GET['searchKey'];
    $result = BookManager::renewBook($bookId,$personId);
    $showInfo = "";
    if ($result !== false) {
      $showInfo = "续借成功";
    } else {
      $showInfo = "续借失败";
    }
    echo '<script>history.back();alert("'.$showInfo.'");</script>';
    $books    = showData($page,$pageSize,$key);
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
        <h3 id="store_h3">图书列表</h3>
        <form id="tab" class="form-horizontal " method="GET" action="bookManager.php">
      <div class="control-group">
          <input type="text" name="searchKey" class="input-xlarge">
          <button class="btn btn-primary" type="submit" id="submit">搜索</button>
      </div>
    </form>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>编号</th>
              <th>书名</th>
              <th>状态</th>
              <th>借阅人</th>
              <th>借阅时间</th>
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
                  echo '<tr>'
                      .'<td>'.$book->sunccoNo.'</td>'
                      .'<td><a href="/admin/book/bookDetail.php?bookId='.$book->bookId.'">'.$book->name.'</a></td>'
                      .'<td>'.$bookStatuStr.'</td>'
                      .'<td>'.$book->currentRecord->personName.'</td>'
                      .'<td>'.$book->currentRecord->borrowTime.'</td>'
                      .'<td>'.$renewButtonHtml.'</td>'
                      .'</tr>';
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
                   ."pageNav.url=\"/admin/book/bookManager.php?searchKey=$books[key]&page={index}&pageSize=$pageSize\";"
                   ."pageNav.fn = function(p,pn){"
                   ."};"
                   ."pageNav.go($books[currentPage],$books[pageSum]);"
                  ."</script>";
  ?>
</div>
</div>
<?php require_once "../adminFooter.html"; ?></body>
</html>