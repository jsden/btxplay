DROP TABLE IF EXISTS `my_city`;
CREATE TABLE `my_city` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `REGION_ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `my_city` WRITE;
INSERT INTO `my_city` VALUES (1,'Ульяновск',1),(2,'Димитровград',1),(3,'Нью-Васюки',2),(4,'Старые Васюки',2);
UNLOCK TABLES;

DROP TABLE IF EXISTS `my_region`;
CREATE TABLE `my_region` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `my_region` WRITE;
INSERT INTO `my_region` VALUES (1,'Ульяновская'),(2,'Новочукотская');
UNLOCK TABLES;
