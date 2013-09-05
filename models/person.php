<?php
require_once("database.php");
/**
 * 成员类
 */
class Person {
    public $personId;
    public $name;
    public $sunccoNo;
    public $type;

    //附加信息
    public $borrowBookCount;
    public $allBorrowBookCount;

    public function __toString() {
        return  'person:'.$this->personId.','.$this->name.','.$this->sunccoNo.','.$this->type;
    }

}

/**
 * 图书管理类
 */
class PersonManager {

    /**
     * 添加成员
     *
     * @param Person  $person 要添加的数据对象，会自动给personId和addTime赋值
     * @return bool 返回添加成员结果
     */
    public static function addPerson( Person &$person ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "insert into person (name,suncco_no) values (?,?)";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, $person->name );
            $stmt->bindParam( 2, $person->sunccoNo );

            $stmt->execute();
            $person->personId = $dataBaseHandler->lastInsertId( "id" );
        } catch( PDOExecption $e ) {
            print "添加成员失败: " . $e->getMessage() . "</br>";
            return false;
        }

        return true;
    }

    /**
     * 编辑成员
     *
     * @param Person  newPerson 编辑后的成员对象
     * @param string  personId 要编辑的成员id
     * @return bool 编辑结果
     */
    public static function editPerson( Person $newPerson , $personId ) {

        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "update person set name=?,suncco_no=?,type=? where id=?;";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, $newPerson->name );
            $stmt->bindParam( 2, $newPerson->sunccoNo );
            $stmt->bindParam( 3, $newPerson->type );
            $stmt->bindParam( 4, $personId );

            $stmt->execute();
        } catch( PDOExecption $e ) {
            print "编辑成员失败: " . $e->getMessage() . "</br>";
            return false;
        }

        return true;

    }

    /**
     * 删除成员
     *
     * @param string  personId 要删除的书本id
     * @return  bool 删除结果
     */
    public static function delPerson( $personId ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "delete from person where id = ?;";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, $personId );

            $stmt->execute();
        } catch( PDOExecption $e ) {
            print "删除成员失败: " . $e->getMessage() . "</br>";
            return false;
        }

        return true;
    }

    /**
     * 获得成员
     *
     * @param string  personId 要获得的成员id
     * @return Person 获得的成员，如果出现错误则返回false
     */
    public static function getPerson( $personId ) {

        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "select * from person where id = ?;";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, $personId );

            $stmt->execute();
            if ( $row = $stmt->fetch() ) {
                $tempPerson = new Person();

                $tempPerson->personId = $row['id'];
                $tempPerson->name     = $row['name'];
                $tempPerson->sunccoNo = $row['suncco_no'];
                $tempPerson->type     = $row['type'];

                return $tempPerson;
            } else {
                throw new Exception( "获得成员失败:没有找到" );
            }

        } catch( PDOExecption $e ) {
            print "获得成员失败: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "获得成员失败: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    /**
     * 搜索成员
     *
     * @param string  personName 成员名
     * @param int     page 搜索结果的页数
     * @param int     size 搜索结果每页的数量
     * @return  array 搜索结果，如果发生错误，则返回false
     */
    public static function searchPersons( $personName, $page=1, $size=10 ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql      = "select * from person ";

            if ( strlen( $personName ) != 0 ) {
                $sql = "select * from person  where name like '%".$personName."%' ";
            }

            if ( $size != -1 ) {
                $sql  = $sql." limit ".( ( $page-1 ) * $size ).",$size ;";
            }
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->execute();
            for ( $i = 0;$row = $stmt->fetch();$i++ ) {
                $tempPerson = new Person();

                $tempPerson->personId = $row['id'];
                $tempPerson->name     = $row['name'];
                $tempPerson->sunccoNo = $row['suncco_no'];
                $tempPerson->type     = $row['type'];
                
                $resultPerson['persons'][$i] = $tempPerson;
            }

            $count = $stmt->rowCount();
            $stmt->closeCursor();
            $pageSum      = intval( $count / $size );
            $resultPerson['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
            $resultPerson['currentPage']  = $page;
            return $resultPerson;

        } catch( PDOExecption $e ) {
            print "搜索成员失败: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "搜索成员失败: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    /**
     * 搜索成员
     *
     * @param string  sunccoNo 成员工号
     * @param int     page 搜索结果的页数
     * @param int     size 搜索结果每页的数量
     * @return  array 搜索结果，如果发生错误，则返回false
     */
    public static function searchPersonsBySunccoNo( $sunccoNo, $page=1, $size=10 ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql      = "select * from person ";

            if ( strlen( $sunccoNo ) != 0 ) {
                $sql = "select * from person  where suncco_no like '%".$sunccoNo."%' ";
            }

            if ( $size != -1 ) {
                $sql  = $sql." limit ".( ( $page-1 ) * $size ).",$size ;";
            }
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->execute();
            for ( $i = 0;$row = $stmt->fetch();$i++ ) {
                $tempPerson = new Person();

                $tempPerson->personId = $row['id'];
                $tempPerson->name     = $row['name'];
                $tempPerson->sunccoNo = $row['suncco_no'];
                $tempPerson->type     = $row['type'];
                
                $resultPerson['persons'][$i] = $tempPerson;
            }

            $count = $stmt->rowCount();
            $stmt->closeCursor();
            $pageSum      = intval( $count / $size );
            $resultPerson['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
            $resultPerson['currentPage']  = $page;
            return $resultPerson;

        } catch( PDOExecption $e ) {
            print "搜索成员失败: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "搜索成员失败: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    /**
     * 搜索成员附带订阅信息
     *
     * @param string  personName 成员名
     * @param int     page 搜索结果的页数
     * @param int     size 搜索结果每页的数量
     * @return  array 搜索结果，如果发生错误，则返回false
     */
    public static function searchPersonsWithRecordInfo( $personName, $page=1, $size=10 ) {
        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql      = "select a.*,(select count(*) from record where record.person_id = a.id and record.status = 1) as count " 
                      ."from person as a ";

            if ( strlen( $personName ) != 0 ) {
                $sql = $sql." where a.name like '%".$personName."%' ";
            }

            $countSql = $sql;
            if ( $size != -1 ) {
                $sql  = $sql." limit ".( ( $page-1 ) * $size ).",$size ;";
            }

            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->execute();
            for ( $i = 0;$row = $stmt->fetch();$i++ ) {
                $tempPerson = new Person();

                $tempPerson->personId = $row['id'];
                $tempPerson->name     = $row['name'];
                $tempPerson->sunccoNo = $row['suncco_no'];
                $tempPerson->type     = $row['type'];
                $tempPerson->count    = $row['count'];
                
                $resultPerson['persons'][$i] = $tempPerson;
            }
            $stmt->closeCursor();

            $stmt = $dataBaseHandler->prepare($countSql);
            $stmt->execute();
            $count = $stmt->rowCount();
            $stmt->closeCursor();

            $pageSum      = intval( $count / $size );
            $resultPerson['pageSum']   = ( $count % $size != 0 )?( $pageSum+1 ):$pageSum;
            $resultPerson['currentPage']  = $page;
            return $resultPerson;

        } catch( PDOExecption $e ) {
            print "搜索成员失败: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "搜索成员失败: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    /**
     * 获得成员
     *
     * @param string  personId 要获得的成员id
     * @return Person 获得的成员，如果出现错误则返回false
     */
    public static function getPersonWithBorrowCount( $personId ) {

        try {
            $dataBaseHandler =  getDataBaseHander();

            $sql  = "select a.*,"
                  ." (select count(*) from record left join book on record.book_id = book.id "
                  ."where record.person_id = a.id and record.status = 1) as count,"
                  ."(select count(*) from record left join book on record.book_id = book.id "
                  ."where record.person_id = a.id) as allCount from person as a where id = ?;";
            $stmt = $dataBaseHandler->prepare( $sql );

            $stmt->bindParam( 1, $personId );

            $stmt->execute();
            if ( $row = $stmt->fetch() ) {
                $tempPerson = new Person();

                $tempPerson->personId           = $row['id'];
                $tempPerson->name               = $row['name'];
                $tempPerson->sunccoNo           = $row['suncco_no'];
                $tempPerson->type               = $row['type'];
                $tempPerson->borrowBookCount    = $row['count'];
                $tempPerson->allBorrowBookCount = $row['allCount'];

                return $tempPerson;
            } else {
                throw new Exception( "获得成员失败:没有找到" );
            }

        } catch( PDOExecption $e ) {
            print "获得成员失败: " . $e->getMessage() . "</br>";
            return false;
        } catch( Exception $e ) {
            print "获得成员失败: " . $e->getMessage() . "</br>";
            return false;
        }
    }


}
?>
