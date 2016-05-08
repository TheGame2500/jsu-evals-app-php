CREATE TABLE `users` (
      `user_id` int(11) NOT NULL AUTO_INCREMENT,
      `user_name` varchar(100) NOT NULL,
      `user_password_hash` varchar(200) NOT NULL,
      `user_email` varchar(45) NOT NULL,
      PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

