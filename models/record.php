<?php
require_once("database.php");
/**
 * 记录类
 */
class Record {
    public $recordId;
    public $bookId;
    public $personId;
    public $status;
    public $borrowTime;
    public $remandTime;

    //非数据库表明
    public $personName;
    public $bookName;
    public $personEmail;

    public function __toString() {
        return  'record:'.$this->recordId.','.$this->bookId.','.$this->personId.','
            .$this->status.','.$this->borrowTime.','.$this->remandTime;
    }

}

/**
 * 记录管理类
 */
class RecordManager {

    /**
     * 添加记录
     *
     * @param Record  $record 要添加的数据对象，会自动给recordId和addTime赋值
     * @return bool 返回添加记录结果
     */
    public static function addRecord( Record &$record ) {
        $record->borrowTime = date( "Y-m-d h:i:s" );
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "insert into record (book_id,person_id,borrow_time) values (?,?,?)";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, $record->bookId );
            $stmt->bindParam( 2, $record->personId );
            $stmt->bindParam( 3, $record->borrowTime );

            $stmt->execute();
            $record->recordId = $dataBaseHandler->lastInsertId( "id" );
        } catch( PDOExecption $e ) {
            print "添加记录失败: " . $e->getMessage() . "</br>";
            return false;
        }

        return true;
    }

    /**
     * 添加返还时间
     *
     * @param string  recordId 要编辑的记录id
     * @return bool 添加结果
     */
    public static function addRemandTime( $recordId ) {

        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "update record set status=0,remand_time=? where id=?;";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, date( "Y-m-d h:i:s" ) );
            $stmt->bindParam( 2, $recordId );

            $stmt->execute();
        } catch( PDOExecption $e ) {
            print "编辑记录失败: " . $e->getMessage() . "</br>";
            return false;
        }

        return true;

    }

    /**
     * 删除记录
     *
     * @param string  recordId 要删除的记录id
     * @return  bool 删除结果
     */
    public static function delRecord( $recordId ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "delete from record where id = ?;";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, $recordId );

            $stmt->execute();
        } catch( PDOExecption $e ) {
            print "删除记录失败: " . $e->getMessage() . "</br>";
            return false;
        }

        return true;
    }

    

    /**
     * 获得记录
     *
     * @param string  recordId 要获得的记录id
     * @return Record 获得的记录，如果出现错误则返回false
     */
    public static function getRecord( $recordId ) {

        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "select * from record where id = $recordId;";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->execute();
            if ( $row = $stmt->fetch() ) {
                $tempRecord = new Record();

                $tempRecord->recordId   = $row['id'];
                $tempRecord->bookId     = $row['book_id'];
                $tempRecord->personId   = $row['person_id'];
                $tempRecord->status     = $row['status'];
                $tempRecord->borrowTime = $row['borrow_time'];
                $tempRecord->remandTime = $row['remand_time'];

                return $tempRecord;
            } else {
                throw new Exception( "获得记录失败:没有找到" );
            }

        } catch( PDOExecption $e ) {
            print "获得记录失败1: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "获得记录失败1: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    /**
     * 通过一些参数获得记录
     *
     * @param string  personId 记录的成员id
     * @param string  bookId 记录的书籍id
     * @param int     status 记录的状态
     * @return Record 获得的记录，如果出现错误则返回false
     */
    public static function getRecordWithParams( $personId, $bookId, $status ) {

        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "select * from record where person_id = ? and book_id = ? and status = ?;";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, $personId );
            $stmt->bindParam( 2, $bookId );
            $stmt->bindParam( 3, $status );

            $stmt->execute();
            if ( $row = $stmt->fetch() ) {
                $tempRecord = new Record();

                $tempRecord->recordId   = $row['id'];
                $tempRecord->bookId     = $row['book_id'];
                $tempRecord->personId   = $row['person_id'];
                $tempRecord->status     = $row['status'];
                $tempRecord->borrowTime = $row['borrow_time'];
                $tempRecord->remandTime = $row['remand_time'];

                return $tempRecord;
            } else {
                throw new Exception( "获得记录失败:没有找到" );
            }

        } catch( PDOExecption $e ) {
            print "获得记录失败2: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "获得记录失败2: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    /**
     * 搜索记录
     *
     * @param string  personId 记录的成员id
     * @param string  bookId 记录的书籍id
     * @param int     status 记录的状态
     * @param int     page 搜索结果的页数
     * @param int     size 搜索结果每页的数量
     * @return  array 搜索结果，如果发生错误，则返回false
     */
    public static function searchRecords( $personId=-1, $bookId=-1, $status=-1, $page=1, $size=10 ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql      = "select * from record ";

            if ( $personId != -1 ) {
                $sql = $sql." where person_id = $personId";
            } else {
                $sql = $sql." where person_id != -1 ";
            }

            if ( $bookId != -1 ) {
                $sql = $sql." and book_id = $bookId";
            } else {
                $sql = $sql." and book_id != -1 ";
            }

            if ( $status != -1 ) {
                $sql = $sql." and status = $status";
            } else {
                $sql = $sql." and status != -1 ";
            }

            if ( $size != -1 ) {
                $sql  = $sql." limit ".( ( $page-1 ) * $size ).",$size ;";
            }

            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->execute();
            for ( $i = 0;$row = $stmt->fetch();$i++ ) {
                $tempRecord = new Record();

                $tempRecord->recordId   = $row['id'];
                $tempRecord->bookId     = $row['book_id'];
                $tempRecord->personId   = $row['person_id'];
                $tempRecord->status     = $row['status'];
                $tempRecord->borrowTime = $row['borrow_time'];
                $tempRecord->remandTime = $row['remand_time'];

                $resultRecord['records'][$i] = $tempRecord;
            }

            $count = $stmt->rowCount();
            $stmt->closeCursor();
            $pageSum      = intval( $count / $size );
            $resultRecord['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
            $resultRecord['currentPage']  = $page;
            return $resultRecord;

        } catch( PDOExecption $e ) {
            print "搜索记录失败: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "搜索记录失败: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    /**
     * 搜索记录包含借阅信息
     *
     * @param string  bookId 记录的书籍id
     * @param int     status 记录的状态
     * @param int     page 搜索结果的页数
     * @param int     size 搜索结果每页的数量
     * @return  array 搜索结果，如果发生错误，则返回false
     */
    public static function searchRecordsWithPersonName($bookId, $status=-1, $page=1, $size=10 ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql      = "select record.*,person.name as personName from record left join person on record.person_id = person.id where book_id = $bookId";

            if ( $status != -1 ) {
                $sql = $sql." and status = $status";
            } else {
                $sql = $sql." and status != -1 ";
            }

            if ( $size != -1 ) {
                $sql  = $sql." order by borrow_time desc limit ".( ( $page-1 ) * $size ).",$size ;";
            }

            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->execute();
            for ( $i = 0;$row = $stmt->fetch();$i++ ) {
                $tempRecord = new Record();

                $tempRecord->recordId   = $row['id'];
                $tempRecord->bookId     = $row['book_id'];
                $tempRecord->personId   = $row['person_id'];
                $tempRecord->status     = $row['status'];
                $tempRecord->borrowTime = $row['borrow_time'];
                $tempRecord->remandTime = $row['remand_time'];
                $tempRecord->personName = $row['personName'];

                $resultRecord['records'][$i] = $tempRecord;
            }

            $count = $stmt->rowCount();
            $stmt->closeCursor();
            $pageSum      = intval( $count / $size );
            $resultRecord['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
            $resultRecord['currentPage']  = $page;
            return $resultRecord;

        } catch( PDOExecption $e ) {
            print "搜索记录失败: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "搜索记录失败: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    /**
     * 搜索记录包含借阅信息
     *
     * @param string  personId 记录的成员id
     * @param int     status 记录的状态
     * @param int     page 搜索结果的页数
     * @param int     size 搜索结果每页的数量
     * @return  array 搜索结果，如果发生错误，则返回false
     */
    public static function searchRecordsWithBookName($personId, $status=-1, $page=1, $size=10 ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql      = "select record.*,book.name as bookName from record left join book on record.book_id = book.id "
                      ."where person_id =$personId ";

            if ( $status != -1 ) {
                $sql = $sql." and record.status = $status";
            } else {
                $sql = $sql." and record.status != -1 ";
            }

            if ( $size != -1 ) {
                $sql  = $sql." order by borrow_time desc limit ".( ( $page-1 ) * $size ).",$size ;";
            }

            $stmt = $dataBaseHandler->prepare( $sql );
            
            $stmt->execute();
            for ( $i = 0;$row = $stmt->fetch();$i++ ) {
                $tempRecord = new Record();

                $tempRecord->recordId   = $row['id'];
                $tempRecord->bookId     = $row['book_id'];
                $tempRecord->personId   = $row['person_id'];
                $tempRecord->status     = $row['status'];
                $tempRecord->borrowTime = $row['borrow_time'];
                $tempRecord->remandTime = $row['remand_time'];
                $tempRecord->bookName   = $row['bookName'];

                $resultRecord['records'][$i] = $tempRecord;
            }

            $count = $stmt->rowCount();
            $stmt->closeCursor();
            $pageSum      = intval( $count / $size );
            $resultRecord['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
            $resultRecord['currentPage']  = $page;
            return $resultRecord;

        } catch( PDOExecption $e ) {
            print "搜索记录失败: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "搜索记录失败: " . $e->getMessage() . "</br>";
            return false;
        }
    }


}
?>
