CREATE DATABASE crud_db_manajementdataevent;
USE crud_db_manajementdataevent;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    remember_token VARCHAR(255) DEFAULT NULL 
);