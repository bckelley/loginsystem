-- drop database
DROP DATABASE IF Exists loginsystem;

-- create database
CREATE DATABASE loginsystem;

-- use database
USE loginsystem;

-- creating table
CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    forgot_pass_identity VARCHAR(32) NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL,
    status ENUM('1','0') NOT NULL,
	isAdmin ENUM('0','1') NOT NULL
);

-- insert default user admin with password administrator
INSERT INTO  `users` (`username`, `password`, `email`) VALUES("admin", "200ceb26807d6bf99fd6f4f0d1ca54d4", "admin@loginsystem.app");

SELECT * FROM users;