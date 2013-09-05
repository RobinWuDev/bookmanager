<?php require 'head.php'; ?>
<style type="text/css">
.form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
.form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
}
.form-signin input[type="text"],
.form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
}
</style>

<?php
require_once './models/book.php';
require_once './models/record.php';
if ( $_SERVER['REQUEST_METHOD']=='GET' ) {
    $page     = $_GET['page'];
    $pageSize = $_GET['pageSize'];
    $key      = $_GET['searchKey'];
    if ( empty( $page ) ) {
        $page = 1;
    }
    if ( empty( $pageSize ) ) {
        $pageSize = 10;
    }

    if ( empty( $key ) ) {
        $key = "";
    }
    $books = BookManager::searchBooksWithRecordInfo( $key, $page, $pageSize );
    if ( $books === false || count( $books["books"] ) == 0 ) {
        echo "<script>history.back();alert('没有数据');</script>";
        return;
    }
}

?>

<div class="container-fluid">
    <div class="row-fluid">

      <div id="content">
        <h3 id="store_h3">图书列表</h3>
        <form id="tab" class="form-horizontal " method="GET" action="index.php">
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
            </tr>
          </thead>
          <tbody id="store_list">
            <?php
foreach ( $books['books'] as $book ) {
    $bookStatuStr = "";
    if ( $book->status == 0 ) {
        $bookStatuStr = "在库";
    } else if ( $book->status == -1 ) {
            $bookStatuStr = "丢失";
        } else {
        $bookStatuStr = "借出";
    }
    echo "<tr>"
        ."<td>".$book->sunccoNo."</td>"
        ."<td><a href='bookDetail.php?bookId=$book->bookId'>".$book->name."</a></td>"
        ."<td>".$bookStatuStr."</td>"
        ."<td>".$book->currentRecord->personName."</td>"
        ."<td>".$book->currentRecord->borrowTime."</td>"
        ."</tr>";
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
    ."pageNav.url=\"/index.php?page={index}&pageSize=$pageSize\";"
    ."pageNav.fn = function(p,pn){"
    ."};"
    ."pageNav.go($books[currentPage],$books[pageSum]);"
    ."</script>";
?>
</div>
</div>
<?php require 'foot.php'; ?>
