<?php
  session_start();
  if (!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
      Header("Location:"."http://".$_SERVER['HTTP_HOST']."/login.php"); 

  }
?>
<meta charset="utf-8">
<title>尚科图书管理系统</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="LS.Dev">
<script src="/static/js/external/jquery-1.9.0.min.js"></script>
<script src="/static/js/external/bootstrap.min.js"></script>
<!-- Le styles -->

<link href="/static/css/bootstrap.css" rel="stylesheet">
<link href="/static/css/style.css" ref="stylesheet">
<link rel="stylesheet" type="text/css" href="/static/css/demo.css" />
<link rel="shortcut icon" href="http://www.suncco.com/templates/2013/web/public/images/favicon.ico" type="image/x-icon">
<style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
      .pageNav a{
        font-weight: bold;
        color:#2B4A78; 
        text-decoration:none;
        border-color: #E3E3E3;
        background: #EEE;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        border-width: 1px;
        border-style: solid;
        }
        .pageNav a:hover { 
        color:#2B4A78;
        text-decoration:underline; 
        }
        .pageNav a:focus, input:focus {
        outline-style:none; 
        outline-width:medium; }
        /* custom css style: pageNum,cPageNum */
        .pageNum{
        border: 1px solid #999;
        padding:2px 8px;
        display: inline-block;
        }
        .cPageNum{
        font-weight: bold;
        padding:2px 5px;
        }
        .pageNav a:hover{
        text-decoration:none;
        background: #fff4d8; 
        }
</style>