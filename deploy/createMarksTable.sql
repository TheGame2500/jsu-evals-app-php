CREATE TABLE `marks` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `nota_formular` decimal(10,0) DEFAULT NULL,
      `nota_recomandare` decimal(10,0) DEFAULT NULL,
      `nota_voluntariat` decimal(10,0) DEFAULT NULL,
      `form_id` int(11) NOT NULL,
      `medie` decimal(10,0) NOT NULL,
      `user_id` int(11) NOT NULL,
      `user_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
