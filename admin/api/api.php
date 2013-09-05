<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  require_once '../../models/record.php';
  require_once '../../models/book.php';
  require_once '../../models/person.php';
  require_once '../../models/tool.php';
  $action = $_GET['action'];
  $query = $_GET['query'];
  $type = $_GET['type'];
  switch ($action) {
    case 'getbooks':
      {
        if ($type == 0) {
          $books = BookManager::searchBooks($query, 1, -1);
        } else {
          $books = BookManager::searchBooksBySunccoNo($query, 1, -1);
        }

        if ($books === false) {
          return;
        }
        $bookNames = array();

        $bookCount = count($books['books']);

        for ($i = 0; $i < $bookCount; $i++) {
          $book = $books['books'][$i];
          $bookNames[$i] = $book->name."_".$book->bookId."_".$book->sunccoNo;
        }
        echo JSON($bookNames);
        return;

      }
      break;
    case 'getpersons':
      {
        if ($type == 0) {
          $persons = PersonManager::searchPersons($query, 1, -1);
        } else {
          $persons = PersonManager::searchPersonsBySunccoNo($query, 1, -1);
        }

        if ($persons === false) {
          return;
        }
        $personNames = array();

        $personCount = count($persons['persons']);

        for ($i = 0; $i < $personCount; $i++) {
          $person = $persons['persons'][$i];
          $personNames[$i] = $person->name."_".$person->personId."_".$person->sunccoNo;
        }
        echo JSON($personNames);
        return;

      }
      break;
    case 'getBorrowerInfo':
      {
        if (strlen($query) != 0) {
          $person = BookManager::getPerson($query);
          if ($person === false) {
            echo "";
          } else {
            echo $person->name."_".$person->personId."_".$person->sunccoNo;
          }
        }
        
      }
      break;
    default:
      break;
  }
} 
?>