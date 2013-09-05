<?php 
    require_once('../../models/person.php');
   if($_SERVER['REQUEST_METHOD']=='GET'){
      $page     = $_GET['page'];
      $pageSize = $_GET['pageSize'];
      $key      = $_GET['searchKey'];
      if (empty($page)) {
        $page = 1;
      }
      if (empty($pageSize)) {
        $pageSize = 10;
      }

      if (empty($key)) {
        $key = "";
      }
      $persons = PersonManager::searchPersonsWithRecordInfo($key,$page,$pageSize);
      if ($persons === false || count($persons["persons"]) == 0) {
        echo "<script>history.back();alert('没有数据');</script>";
        return;
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

      <div class="span9" id="content">
    <h3 id="store_h3">人员列表</h3>
    <form id="tab" class="form-horizontal " method="GET" action="personManager.php">
      <div class="control-group">
          <input type="text" name="searchKey" class="input-xlarge">
          <button class="btn btn-primary" type="submit" id="submit">搜索</button>
      </div>
    </form>
    <table class="table table-hover">
        <thead>
              <tr>
                <th>编号</th>
                <th>姓名</th>
                <th>未还书数</th>
              </tr>
         </thead>
        <tbody id="store_list">
             <?php
              
                foreach ($persons['persons'] as $person) {
                  echo "<tr>"
                        ."<td>".$person->sunccoNo."</td>"
                        ."<td><a href='/admin/person/personDetail.php?personId=$person->personId'>".$person->name."</a></td>"
                        ."<td>".$person->count."</td>"
                        ."</tr>";
                }
             ?>
         </tbody>
    </table>
     <div id="pageNavId" class="span9 offset2" style="text-align: right;margin-bottom: 30px;">
    </div>
</div>
    </div>
  </div>
<?php require_once "../adminFooter.html"; ?>
</body>
</html>

<script src="/static/js/external/pagenav1.1.min.js"></script>
<?php
echo "<script type=\"text/javascript\">"
                   ."pageNav.pageNavId=\"pageNavId\";"
                   ."pageNav.pre=\"上一页\";"
                   ."pageNav.next=\"下一页\";"
                   ."pageNav.url=\"/admin/person/personManager.php?page={index}&pageSize=$pageSize\";"
                   ."pageNav.fn = function(p,pn){"
                   ."};"
                   ."pageNav.go($persons[currentPage],$persons[pageSum]);"
                  ."</script>";
?>