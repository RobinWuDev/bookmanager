<?php
require_once("database.php");
require_once("person.php");
require_once("record.php");
/**
 * 书籍类
 */
class Book {
	public $bookId;
	public $name;
	public $price;
	public $ISBN;
	public $sunccoNo;
	public $addTime;
	public $status;

	//附加信息
	public $currentRecord;
	public $count;
	public $expiredDays;

	public function __toString() {
		return  'book:'.$this->bookId.','.$this->name.','.$this->price.','
			.$this->ISBN.','.$this->sunccoNo.','.$this->addTime.','
			.$this->status.','.$this->expiredDays;
	}

}

/**
 * 图片管理类
 */
class BookManager {

	/**
	 * 添加书籍
	 *
	 * @param Book    $book 要添加的数据对象，会自动给bookId和addTime赋值
	 * @return bool 返回添加书籍结果
	 */
	public static function addBook( Book &$book ) {
		$book->addTime = date( "Y-m-d h:i:s" );
		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql  = "insert into book (name,isbn,suncco_no,price,add_time) values (?,?,?,?,?)";
			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->bindParam( 1, $book->name );
			$stmt->bindParam( 2, $book->ISBN );
			$stmt->bindParam( 3, $book->sunccoNo );
			$stmt->bindParam( 4, $book->price );
			$stmt->bindParam( 5, $book->addTime );

			$stmt->execute();
			$book->bookId = $dataBaseHandler->lastInsertId( "id" );
		} catch( PDOExecption $e ) {
			print "添加书籍失败: " . $e->getMessage() . "</br>";
			return false;
		}

		return true;
	}

	/**
	 * 编辑书籍
	 *
	 * @param Book    newBook 编辑后的书籍对象
	 * @param string  bookId 要编辑的书籍id
	 * @return bool 编辑结果
	 */
	public static function editBook( Book $newBook , $bookId ) {

		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql  = "update book set name=?,isbn=?,suncco_no=?,price=?,status=? where id=?;";
			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->bindParam( 1, $newBook->name );
			$stmt->bindParam( 2, $newBook->ISBN );
			$stmt->bindParam( 3, $newBook->sunccoNo );
			$stmt->bindParam( 4, $newBook->price );
			$stmt->bindParam( 5, $newBook->status );
			$stmt->bindParam( 6, $bookId );

			$stmt->execute();
		} catch( PDOExecption $e ) {
			print "编辑书籍失败: " . $e->getMessage() . "</br>";
			return false;
		}

		return true;

	}

	/**
	 * 删除书籍
	 *
	 * @param string  bookId 要删除的书本id
	 * @return  bool 删除结果
	 */
	public static function delBook( $bookId ) {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql  = "delete from book where id = ?;";
			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->bindParam( 1, $bookId );

			$stmt->execute();
		} catch( PDOExecption $e ) {
			print "删除书籍失败: " . $e->getMessage() . "</br>";
			return false;
		}

		return true;
	}

	/**
	 * 获得书籍
	 *
	 * @param string  bookId 要获得的书籍id
	 * @return Book 获得的书籍，如果出现错误则返回false
	 */
	public static function getBook( $bookId ) {

		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql  = "select a.*,(select count(*) from record where record.book_id = a.id)as count from book as a where id = ?;";
			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->bindParam( 1, $bookId );

			$stmt->execute();
			if ( $row = $stmt->fetch() ) {
				$tempBook = new Book();

				$tempBook->bookId   = $row['id'];
				$tempBook->name     = $row['name'];
				$tempBook->ISBN     = $row['isbn'];
				$tempBook->sunccoNo = $row['suncco_no'];
				$tempBook->addTime  = $row['add_time'];
				$tempBook->status   = $row['status'];
				$tempBook->count    = $row['count'];
				return $tempBook;
			} else {
				throw new Exception( "获得书籍失败:没有找到" );
			}

		} catch( PDOExecption $e ) {
			print "获得书籍失败: " . $e->getMessage() . "</br>";
			return false;
		} catch( Exception $e ) {
			print "获得书籍失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 搜索书籍
	 *
	 * @param string  bookName 书籍名
	 * @param int     page 搜索结果的页数
	 * @param int     size 搜索结果每页的数量
	 * @return  array 搜索结果，如果发生错误，则返回false
	 */
	public static function searchBooks( $bookName, $page=1, $size=10 ) {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql      = "select * from book ";

			if ( strlen( $bookName ) != 0 ) {
				$sql = "select * from book  where name like '%".$bookName."%' ";
			}

			if ( $size != -1 ) {
				$sql  = $sql." limit ".( ( $page-1 ) * $size ).",$size ;";
			}
			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->execute();
			$resultBook['books'] = array();
			for ( $i = 0;$row = $stmt->fetch();$i++ ) {
				$tempBook = new Book();

				$tempBook->bookId        = $row['id'];
				$tempBook->name          = $row['name'];
				$tempBook->ISBN          = $row['isbn'];
				$tempBook->sunccoNo      = $row['suncco_no'];
				$tempBook->addTime       = $row['add_time'];
				$tempBook->status        = $row['status'];
				$resultBook['books'][$i] = $tempBook;
			}

			$count = $stmt->rowCount();
			$stmt->closeCursor();
			$pageSum      = intval( $count / $size );
			$resultBook['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
			$resultBook['currentPage']  = $page;
			$resultBook['key']  = $bookName;
			return $resultBook;

		} catch( PDOExecption $e ) {
			print "搜索书籍失败: " . $e->getMessage() . "</br>";
			return false;
		} catch( Exception $e ) {
			print "搜索书籍失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 搜索书籍
	 *
	 * @param string  sunccoNo 书籍编号
	 * @param int     page 搜索结果的页数
	 * @param int     size 搜索结果每页的数量
	 * @return  array 搜索结果，如果发生错误，则返回false
	 */
	public static function searchBooksBySunccoNo( $sunccoNo, $page=1, $size=10 ) {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql      = "select * from book ";

			if ( strlen( $sunccoNo ) != 0 ) {
				$sql = "select * from book  where suncco_no like '%".$sunccoNo."%' ";
			}

			if ( $size != -1 ) {
				$sql  = $sql." limit ".( ( $page-1 ) * $size ).",$size ;";
			}
			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->execute();
			$resultBook['books'] = array();
			for ( $i = 0;$row = $stmt->fetch();$i++ ) {
				$tempBook = new Book();

				$tempBook->bookId        = $row['id'];
				$tempBook->name          = $row['name'];
				$tempBook->ISBN          = $row['isbn'];
				$tempBook->sunccoNo      = $row['suncco_no'];
				$tempBook->addTime       = $row['add_time'];
				$tempBook->status        = $row['status'];
				$resultBook['books'][$i] = $tempBook;
			}

			$count = $stmt->rowCount();
			$stmt->closeCursor();
			$pageSum      = intval( $count / $size );
			$resultBook['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
			$resultBook['currentPage']  = $page;
			$resultBook['key']  = $sunccoNo;
			return $resultBook;

		} catch( PDOExecption $e ) {
			print "搜索书籍失败: " . $e->getMessage() . "</br>";
			return false;
		} catch( Exception $e ) {
			print "搜索书籍失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 搜索书籍包含当前的订阅情况
	 *
	 * @param string  bookName 书籍名
	 * @param int     page 搜索结果的页数
	 * @param int     size 搜索结果每页的数量
	 * @return  array 搜索结果，如果发生错误，则返回false
	 */
	public static function searchBooksWithRecordInfo( $bookName, $page=1, $size=10 ) {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql  = "select book.id as id,book.name as bookName,book.isbn as isbn,book.suncco_no as sunccoNo,"
					  . "book.status as status,person.id as personId,person.name as personName,record.borrow_time as borrowTime from book "
					  . "left join record on (book.id = record.book_id and book.status = 1 and record.status = 1) "
				      ."left join person on record.person_id = person.id  ";

			if ( strlen( $bookName ) != 0 ) {
				$sql  = $sql." where book.name like '%".$bookName."%' ";
			} 
				
			$sql = $sql."order by id ";

			$countSql = $sql;
			if ( $size != -1 ) {
				$sql  = $sql." limit ".( ( $page-1 ) * $size ).",$size ;";
			}

			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->execute();
			$resultBook['books'] = array();
			for ( $i = 0;$row = $stmt->fetch();$i++ ) {
				$tempBook = new Book();

				$tempBook->bookId        = $row['id'];
				$tempBook->name          = $row['bookName'];
				$tempBook->ISBN          = $row['isbn'];
				$tempBook->sunccoNo      = $row['sunccoNo'];
				$tempBook->status        = $row['status'];

				$bookRecord = new Record();

				$bookRecord->personId    = $row['personId'];
				$bookRecord->personName  = $row['personName'];
				$bookRecord->borrowTime  = $row['borrowTime'];

				$tempBook->currentRecord = $bookRecord;

				$resultBook['books'][$i] = $tempBook;
			}

			$stmt->closeCursor();

			$stmt = $dataBaseHandler->prepare($countSql);
			$stmt->execute();
			$count = $stmt->rowCount();
			$stmt->closeCursor();

			$pageSum      = intval( $count / $size );
			$resultBook['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
			$resultBook['currentPage']  = $page;
			$resultBook['key']  = $bookName;

			return $resultBook;

		} catch( PDOExecption $e ) {
			print "搜索书籍失败: " . $e->getMessage() . "</br>";
			return false;
		} catch( Exception $e ) {
			print "搜索书籍失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 获得过期书籍包含当前的订阅情况
	 * @return  array 搜索结果，如果发生错误，则返回false
	 */
	public static function expiredBooksWithRecordInfo() {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql  = "select "
					."book.id as book_id,"
					."book.name as book_name,"
					."book.suncco_no as book_no,"
					."person.id as person_id,"
					."person.name as person_name,"
					."person.suncco_no as person_no,"
					."record.borrow_time as borrow_time,"
					."record.status as status,"
					."TO_DAYS(NOW()) - TO_DAYS(borrow_time) as borrow_day "
					."FROM record "
					."left join book on record.book_id = book.id "
					."left join person on record.person_id = person.id "
					."where TO_DAYS(NOW()) - TO_DAYS(borrow_time) > 25 and record.status = 1 and book.status = 1; ";		

			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->execute();
			$resultBook['books'] = array();
			for ( $i = 0;$row = $stmt->fetch();$i++ ) {
				$tempBook = new Book();

				$tempBook->bookId        = $row['book_id'];
				$tempBook->name          = $row['book_name'];
				$tempBook->sunccoNo      = $row['book_no'];
				$tempBook->expiredDays   = 31 -  $row['borrow_day'];
				$tempBook->status        = $row['status'];
				$bookRecord = new Record();

				$bookRecord->personId    = $row['person_id'];
				$bookRecord->personName  = $row['person_name'];
				$bookRecord->borrowTime  = $row['borrow_time'];

				$tempBook->currentRecord = $bookRecord;

				$resultBook['books'][$i] = $tempBook;
			}

			$stmt->closeCursor();

			return $resultBook;

		} catch( PDOExecption $e ) {
			print "获取过期书籍失败: " . $e->getMessage() . "</br>";
			return false;
		} catch( Exception $e ) {
			print "获取过期日志失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 获得过期书籍包含当前的订阅情况
	 * @return  array 搜索结果，如果发生错误，则返回false
	 */
	public static function needSendEmailBooks() {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql  = "select "
					."book.id as book_id,"
					."book.name as book_name,"
					."book.suncco_no as book_no,"
					."person.id as person_id,"
					."person.name as person_name,"
					."person.suncco_no as person_no,"
					."record.borrow_time as borrow_time,"
					."record.status as status,"
					."person.email as email,"
					."TO_DAYS(NOW()) - TO_DAYS(borrow_time) as borrow_day "
					."FROM record "
					."left join book on record.book_id = book.id "
					."left join person on record.person_id = person.id "
					."where TO_DAYS(NOW()) - TO_DAYS(borrow_time) = 3 and record.status = 1 and book.status = 1; ";		

			$stmt = $dataBaseHandler->prepare( $sql );

			$stmt->execute();
			$resultBook = array();
			for ( $i = 0;$row = $stmt->fetch();$i++ ) {
				$tempBook = new Book();

				$tempBook->bookId        = $row['book_id'];
				$tempBook->name          = $row['book_name'];
				$tempBook->sunccoNo      = $row['book_no'];
				$tempBook->expiredDays   = 31 -  $row['borrow_day'];
				$tempBook->status        = $row['status'];
				$bookRecord = new Record();

				$bookRecord->personId    = $row['person_id'];
				$bookRecord->personName  = $row['person_name'];
				$bookRecord->borrowTime  = $row['borrow_time'];
				$bookRecord->personEmail = $row['email'];

				$tempBook->currentRecord = $bookRecord;

				$resultBook[$i] = $tempBook;
			}

			$stmt->closeCursor();

			return $resultBook;

		} catch( PDOExecption $e ) {
			print "获取过期书籍失败: " . $e->getMessage() . "</br>";
			return false;
		} catch( Exception $e ) {
			print "获取过期日志失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 获得该书籍的借阅者
	 *
	 * @param string  bookName 书籍名
	 * @param int     page 搜索结果的页数
	 * @param int     size 搜索结果每页的数量
	 * @return  array 搜索结果，如果发生错误，则返回false
	 */
	public static function getPerson( $bookName) {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$sql  = "select person.id as id,person.name as personName,person.suncco_no as sunccoNo,"
					  . "person.type as type from book "
					  . "left join record on book.id = record.book_id "
				      ."left join person on record.person_id = person.id  ";

			if ( strlen( $bookName ) != 0 ) {
				$sql  = $sql." where book.status = 1 and record.status = 1 and book.name = '".$bookName."';";
			} 
			$stmt = $dataBaseHandler->prepare( $sql );
			$stmt->execute();
			if ($row = $stmt->fetch()) {
				$tempPerson = new Person();
				$tempPerson->personId = $row['id'];
                $tempPerson->name     = $row['personName'];
                $tempPerson->sunccoNo = $row['sunccoNo'];
                $tempPerson->type     = $row['type'];
                $stmt->closeCursor();
                return $tempPerson;
			} else {
				return false;
			}
			
		} catch( PDOExecption $e ) {
			print "搜索书籍失败: " . $e->getMessage() . "</br>";
			return false;
		} catch( Exception $e ) {
			print "搜索书籍失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 借书
	 *
	 * @param string  bookId 书籍Id
	 * @param string  personId 成员id
	 * @return  bool 借书结果
	 */
	public static function borrowBook( $bookId,$personId ) {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$changeBookStatusSql = "update book set status = 1 where id = $bookId;";
			$addRecordSql  = "insert into record (book_id,person_id,borrow_time) values ('"
							.$bookId."','".$personId."','".date( "Y-m-d" )."');";

			try {
				$dataBaseHandler->beginTransaction();
				$dataBaseHandler->exec($changeBookStatusSql);
	            $dataBaseHandler->exec($addRecordSql);
	            $dataBaseHandler->commit();
	            return true;
			} catch (PDOExecption $e) {
				$dataBaseHandler->rollBack();
				throw $e;		
			}
			
		} catch( Execption $e ) {
			print "借书失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 借书
	 *
	 * @param string  bookId 书籍Id
	 * @param string  personId 成员id
	 * @return  bool 借书结果
	 */
	public static function borrowBookWithDate( $bookId,$personId,$date) {
		try {
			$dataBaseHandler =  getDataBaseHander();

			$changeBookStatusSql = "update book set status = 1 where id = $bookId;";
			$addRecordSql  = "insert into record (book_id,person_id,borrow_time) values ('"
							.$bookId."','".$personId."','".$date."');";

			try {
				$dataBaseHandler->beginTransaction();
				$dataBaseHandler->exec($changeBookStatusSql);
	            $dataBaseHandler->exec($addRecordSql);
	            $dataBaseHandler->commit();
	            return true;
			} catch (PDOExecption $e) {
				$dataBaseHandler->rollBack();
				throw $e;		
			}
			
		} catch( Execption $e ) {
			print "借书失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 还书
	 *
	 * @param string  bookId 书籍Id
	 * @param string  personId 成员id
	 * @return  bool 还书结果
	 */
	public static function remandBook( $bookId,$personId ) {
		try {
			$dataBaseHandler = getDataBaseHander();

			$changeBookStatusSql = "update book set status = 0 where id = $bookId;";
			$addRecordSql  = "update record set status = 0,remand_time ='".date( "Y-m-d h:i:s" )
							."' where book_id = '$bookId' and person_id = '$personId' and status = 1";

			try {
				$dataBaseHandler->beginTransaction();
	            $dataBaseHandler->exec($changeBookStatusSql);
	            $dataBaseHandler->exec($addRecordSql);
	            $dataBaseHandler->commit();
			} catch (PDOExecption $e) {
				$dataBaseHandler->rollBack();
				throw $e;		
			}
			return true;
		} catch( PDOExecption $e ) {
			print "还书失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}

	/**
	 * 续借
	 *
	 * @param string  bookId 书籍Id
	 * @param string  personId 成员id
	 * @return  bool 还书结果
	 */
	public static function renewBook( $bookId,$personId ) {
		try {
			$dataBaseHandler = getDataBaseHander();

			$remanRecordSql  = "update record set status = 0,remand_time ='".date( "Y-m-d h:i:s" )
							."' where book_id = '$bookId' and person_id = '$personId' and status = 1";
			$borrowRecordSql  = "insert into record (book_id,person_id,borrow_time) values ('"
							.$bookId."','".$personId."','".date( "Y-m-d" )."');";				
			try {
				$dataBaseHandler->beginTransaction();
	            $dataBaseHandler->exec($remanRecordSql);
	            $dataBaseHandler->exec($borrowRecordSql);
	            $dataBaseHandler->commit();
			} catch (PDOExecption $e) {
				$dataBaseHandler->rollBack();
				throw $e;		
			}
			return true;
		} catch( PDOExecption $e ) {
			print "续借失败: " . $e->getMessage() . "</br>";
			return false;
		}
	}




}
?>
