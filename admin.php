<?php require('head.php'); ?>
	
<?php 
	// require('../bookManager/book.inc');

	// $book = new Book();
	// // $book.$name = "name";

	// // BookManager::AddBook($book);
	Header("HTTP/1.1 303 See Other"); 
	Header("Location: login.php"); 

?>

<?php require('foot.php'); ?>


