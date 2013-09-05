
<?php require('head.php'); ?>
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
if($_SERVER['REQUEST_METHOD']=='POST') 
{
  $username = $_POST['username'];
  $password = $_POST['password'];
  if ($username == "admin" && $password == "suncco") {
    session_start();
    $_SESSION['admin'] = true;
    Header("Location:"."http://".$_SERVER['HTTP_HOST']."/admin/book/addBook.php"); 
  } else {
    echo "<script>alert('请输入正确的帐号和密码');</script>";
  }
  
}

?>

<div class="container">

    <form class="form-signin" method="POST" action="login.php">
    <h2 class="form-signin-heading">请登录</h2>
    <input id="username" name = 'username' type="text" class="input-block-level" placeholder="Username">
    <input id="password" name = 'password' type="password" class="input-block-level" placeholder="Password">
    <button id="login_button" class="btn btn-large btn-primary" type="submit">登录</button>
  </form>
   <div>
    <p class="text-error" id="login_prompt_info" align="center"></p>
  </div>
  
</div>
<!-- /container -->

<?php require('foot.php'); ?>