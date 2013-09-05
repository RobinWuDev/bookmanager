<?php
require_once '../../models/book.php';
if ( $_SERVER['REQUEST_METHOD']=='POST' ) {
  $book           = new Book();

  $book->name     = $_POST['name'];
  $book->ISBN     = $_POST['ISBN'];
  $book->sunccoNo = $_POST['sunccoNo'];
  $book->price    = $_POST['price'];

  $ok = BookManager::addBook( $book );

  if ( $ok === false ) {
    echo "添加失败";
  } else {
    $js = "<script>alert('添加成功');</script>";
    echo "$js";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once("../adminHead.php"); ?>
</head>

<body>

  <?php require_once("../adminNav.html"); ?>

  <div class="container-fluid">
    <div class="row-fluid">
      <?php require_once("../adminLeftNav.html"); ?>

      <div class="span9 well" id="content">
        <div class="row-fluid ">
          <form id="tab" class="form-horizontal " method="POST" action="addBook.php">
            
            <?php
              $formList["sunccoNo"]     = "编码 ";
              $formList["name"]         = "书名 ";
              $formList["ISBN"]         = "ISBN";
              $formList["price"]        = "价格 ";

              foreach ( $formList as $key =>$value ) {
              echo "<div class=\"control-group\">";
              echo $value.":"; 
              echo "<input type=\"text\" name='".$key."' class=\"input-xlarge\"><br/>";
              echo "</div>";
              }
            ?>   
            <div class="btn-toolbar">
              <button class="btn btn-primary" type="submit" id="submit">提交</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php require_once("../adminFooter.html"); ?>
</body>
</html>