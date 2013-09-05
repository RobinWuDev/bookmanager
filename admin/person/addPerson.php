<?php
require_once '../../models/person.php';
if ( $_SERVER['REQUEST_METHOD']=='POST' ) {
  $person               = new Person();

  $person->name         = $_POST['name'];
  $person->sunccoNo     = $_POST['personNo'];

  $ok = PersonManager::addPerson( $person );

  if ( $ok === false ) {
    echo "添加失败";
  } else {
    echo "<script>alert('添加成功');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once "../adminHead.php"; ?>
</head>

<body>

  <?php require_once "../adminNav.html"; ?>

  <div class="container-fluid">
    <div class="row-fluid">
      <?php require_once "../adminLeftNav.html"; ?>

      <div class="span9 well" id="content">
        <div class="row-fluid ">
          <form id="tab" class="form-horizontal " method="POST" action="addPerson.php">

            <?php
              $formList["personNo"]     = "工号";
              $formList["name"]         = "姓名";

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
<?php require_once "../adminFooter.html"; ?>
</body>
</html>
