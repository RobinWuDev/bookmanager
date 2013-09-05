图书管理系统
=======

本来是在公司内部用的，很简单，但是因为有一个更好的，所以这个就没用了。

-----

目的：开发一个简单易用的图书管理系统

开发环境：php+linux+mysql

功能：

图书（id,书名,原书名，作者，译者，丛书名，出版社，ISBN,出版时间，开本，页码，版次，类别，数量)

人（id,姓名，工号，类别（管理员，普通））

订阅记录（id，书籍id,书籍名,人id，人名，借书时间，还书时间，状态）


1.图书管理

 1. 添加书籍（管理员）
 2. 删除书籍 （管理员）

 5. 查询图书  (全部)

2.人员管理（添加，查询，删除）

3.订阅（添加，查询，删除）
 
 3. 借阅书籍 （管理员）
 4. 归还书籍 （管理员)
 
 
 数据库创建语句
 `delimiter $$

CREATE DATABASE `bookmanager` /*!40100 DEFAULT CHARACTER SET utf8 */$$

delimiter $$

CREATE TABLE `book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `isbn` varchar(32) DEFAULT NULL,
  `add_time` varchar(32) DEFAULT NULL,
  `suncco_no` varchar(32) DEFAULT NULL,
  `price` float DEFAULT '0',
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=330 DEFAULT CHARSET=utf8$$

delimiter $$

CREATE TABLE `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `suncco_no` varchar(32) DEFAULT NULL,
  `type` int(11) DEFAULT '0',
  `email` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8$$

delimiter $$

CREATE TABLE `record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` varchar(32) DEFAULT NULL,
  `person_id` varchar(32) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `borrow_time` varchar(32) DEFAULT NULL,
  `remand_time` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8$$`
 
 
 

