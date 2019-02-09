create database teste;
use teste;

create table Person(
    id integer primary key auto_increment,
    name varchar(45),
    password varchar(100) unique not null,
    email varchar(60) unique not null,
    tel varchar(45) unique not null
);