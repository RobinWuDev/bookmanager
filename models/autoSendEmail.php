#!/usr/bin/php
<?php 
    require_once("email.php");
    require_once("book.php");
    $result = BookManager::needSendEmailBooks();
    foreach ($result as $book) {
        Email::sendExpiredEmail($book->currentRecord->personName,
                                $book->currentRecord->personEmail,
                                $book->name);
    }
 ?>