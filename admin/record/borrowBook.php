<?php
if ( $_SERVER['REQUEST_METHOD']=='POST' ) {
  require_once '../../models/book.php';
  require_once '../../models/person.php';

  $bookName   = $_POST['bookName'];
  $personName = $_POST['personName'];
  $date       = $_POST['date'];
  try {
    //获取用户对象
    $personName   = split( "_", $personName );
    if ( count( $personName )!=3 ) {
      $errorInfo = "无效的用户名";
      throw new Exception( $errorInfo );
    }

    $person     = PersonManager::getPerson( $personName[1] );
    if ( $person === false ) {
      $errorInfo = "无效的用户名";
      throw new Exception( $errorInfo );
    }

    //获取书籍对象
    $bookName   = split( "_", $bookName );
    if ( count( $bookName )!=3 ) {
      $errorInfo = "无效的书名";
      throw new Exception( $errorInfo );
    }

    $book     = BookManager::getBook( $bookName[1] );
    if ( $book === false ) {
      $errorInfo = "无效的书名";
      throw new Exception( $errorInfo );
    }

    if ( $book->status != 0 ) {
      $errorInfo = "该书不在库中";
      throw new Exception( $errorInfo );
    }


    $ok = BookManager::borrowBookWithDate( $book->bookId, $person->personId,$date);
    if ( $ok === false ) {
      $errorInfo = "操作失败";
      throw new Exception( $errorInfo );
    } else {
      $js = "<script>alert('借书成功');</script>";
      echo "$js";
    }
  } catch ( Exception $e ) {
    $js = "<script>history.back();alert('借书失败:".$e->getMessage()."');</script>";
    echo "$js";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once "../adminHead.php"; ?>
  <link rel="stylesheet" type="text/css" href="/static/js/external/datepicker/css/datepicker.css">
  <script type="text/javascript" src="/static/js/external/datepicker/js/bootstrap-datepicker.js"></script>
</head>

<body>

  <?php require_once "../adminNav.html"; ?>

  <div class="container-fluid">
    <div class="row-fluid">
      <?php require_once "../adminLeftNav.html"; ?>

      <div class="span9 well" id="content">
        <div class="row-fluid ">
          <form id="tab" class="form-horizontal " method="POST" action="borrowBook.php">
            <div class="control-group">
              <select class="input-mini" name="bookType" id="bookType">
                <option value="0">书名</option>
                <option value="1">书号</option>
              </select>
              <input data-provide="typeahead" autocomplete="off" type="text" name='bookName' id='bookName' class="input-xlarge" />
              <br/>
            </div>
            <div class="control-group">
              <select class="input-mini" name="personType" id="personType">
                <option value="0">姓名</option>
                <option value="1">工号</option>
              </select>
              <input  data-provide="typeahead" autocomplete="off" type="text" name='personName' id='personName' class="input-xlarge" />
              <br/>
            </div>
            <?php 
              $date =  date("Y-m-d"); 
            ?>
            <div class="input-append date" id="dp3" data-date='<?php echo $date; ?>' data-date-format="yyyy-mm-dd">
              <input name="date" class="span9" size="16" type="text" value="<?php echo $date; ?>" readonly>
              <span class="add-on">
                  <i class="icon-calendar"></i>
               </span>
            </div>
            <script type="text/javascript">
            $('#dp3').datepicker();
            </script>
            <div class="btn-toolbar">
              <button class="btn btn-primary" type="submit" id="submit">借书</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- <p id="show"></p>
-->
<?php require_once "../adminFooter.html"; ?></body>
</html>

<script type="text/javascript" src="/static/js/controller/borrowRecord.js"></script>
