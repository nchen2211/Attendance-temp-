CREATE DATABASE IF NOT EXISTS attendace; # this is to create the database

CREATE TABLE users (
		id 			smallint unsigned not null auto_increment, 
		username	varchar(50) not null,
		password	varchar(50) not null,
		acct_type	varchar(20) not null,
		pk_users primary key (id)
);

CREATE TABLE classes (
		class_id			smallint unsigned not null auto_increment,
		instructor_name		varchar(50) not null,
		class_num			varchar(20) not null,
		class_time			time not null,
		class_date			varchar(20) not null,		
		pk_classes primary_key (class_id, instructor_name, class_num)
);

CREATE TABLE Room (
		id 					smallint unsigned not null auto_increment, 
		student_name		varchar(100) not null,
		student_email		varchar(50) not null,
		room_num			varchar(5) not null,
		pk_Room primary_key (id, student_email)
);