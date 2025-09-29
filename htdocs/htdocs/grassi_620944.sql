-- Progettazione Web 
DROP DATABASE if exists grassi_620944; 
CREATE DATABASE grassi_620944; 
USE grassi_620944; 
-- MySQL dump 10.13  Distrib 5.7.28, for Win64 (x86_64)
--
-- Host: localhost    Database: grassi_620944
-- ------------------------------------------------------
-- Server version	5.7.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `post` int(11) NOT NULL,
  `application_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `accepted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`post`),
  KEY `post` (`post`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES (100,'alessio_2001',92,'2023-02-06 21:11:29',1),(101,'alessio_2001',94,'2023-02-06 21:52:14',1),(102,'AriannaNardi',91,'2023-02-06 21:11:35',1),(103,'shelbychurch',98,'2023-02-06 21:02:36',1),(104,'leonardocagliostro',92,'2023-02-06 21:11:28',1),(105,'leonardocagliostro',100,'2023-02-06 21:45:57',1),(106,'fedeee',94,'2023-02-06 21:52:15',1),(107,'fedeee',97,'2023-02-06 21:51:59',1),(109,'fedeee',101,'2023-02-06 21:46:46',1),(110,'loregrassi',101,'2023-02-06 21:46:47',1),(111,'nicola20',96,'2023-02-06 21:52:24',1),(112,'alessio_2001',100,'2023-02-06 21:46:02',1),(113,'samu__menchini',101,'2023-02-06 21:46:48',1),(114,'brandonn',98,'2023-02-06 21:19:49',0),(115,'brandonn',102,'2023-02-06 21:20:03',0),(116,'loregrassi',96,'2023-02-06 21:52:25',1),(117,'loregrassi',106,'2023-02-06 21:23:46',0),(118,'_anna_',92,'2023-02-06 21:26:21',0),(119,'_anna_',96,'2023-02-06 21:52:26',1),(120,'_anna_',107,'2023-02-06 21:44:20',1),(122,'loregrassi',100,'2023-02-06 21:46:03',1),(124,'loregrassi',111,'2023-02-06 21:34:46',0),(125,'mirko.graffeo',92,'2023-02-06 21:45:06',0),(126,'mirko.graffeo',91,'2023-02-06 21:45:08',0),(128,'DavideParisi',94,'2023-02-06 21:52:16',1),(129,'mirko.graffeo',117,'2023-02-06 21:45:42',0),(131,'nicole.massari17',92,'2023-02-06 21:56:27',1),(132,'davidecarraesi',92,'2023-02-06 21:52:18',0),(133,'samu__menchini',92,'2023-02-06 21:54:31',0),(134,'samu__menchini',91,'2023-02-06 21:56:15',0);
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_reports`
--

DROP TABLE IF EXISTS `comment_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `comment` int(11) NOT NULL,
  `report_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `denied` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `comment` (`comment`),
  CONSTRAINT `comment_reports_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `comment_reports_ibfk_2` FOREIGN KEY (`comment`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_reports`
--

LOCK TABLES `comment_reports` WRITE;
/*!40000 ALTER TABLE `comment_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `comment_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(20) DEFAULT NULL,
  `post` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `post` (`post`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (131,'2023-02-06 20:48:27','alessio_2001',91,'Figooo però non posso'),(132,'2023-02-06 20:51:32','alessio_2001',96,'Frate vorrei venire ma non ho un euro'),(133,'2023-02-06 20:55:57','singleboy',92,'Uscire ok, ma per andare dove?'),(134,'2023-02-06 20:56:24','singleboy',91,'In quale città esattamente?'),(135,'2023-02-06 21:01:46','shelbychurch',98,'I\'d love tooo! When are you going?'),(136,'2023-02-06 21:02:26','shannonflynn',98,'Idk i havent made plans yet'),(137,'2023-02-06 21:03:50','shelbychurch',103,'English pls'),(138,'2023-02-06 21:05:51','leonardocagliostro',92,'Ci sta'),(139,'2023-02-06 21:06:10','leonardocagliostro',100,'Se vuoi si fa una bevuta in vetto'),(140,'2023-02-06 21:07:00','alessio_2001',101,'Io ci son già andato'),(141,'2023-02-06 21:08:07','JohnnyStecchino',101,'Mi piacerebbe... Ma quanti giorni?'),(142,'2023-02-06 21:10:19','fedeee',94,'Ioooo'),(143,'2023-02-06 21:10:45','fedeee',97,'La sto preparando pure io... se vuoi studiamo insieme'),(144,'2023-02-06 21:14:43','DavideParisi',100,'Si mangia una pizza e si fa un giro in centro. Se ti vuoi unire a noi fai un fischio'),(145,'2023-02-06 21:15:40','DavideParisi',97,'Io ho gli appunti del 2021, credo che il programma non sia cambiato'),(146,'2023-02-06 21:20:21','DavideParisi',94,'Ma c\'è già!!'),(147,'2023-02-06 21:22:10','DavideParisi',92,'C\'è un pub nuovo che ha aperto da poco in borgo stretto. Chi viene per una bevuta?'),(148,'2023-02-06 21:23:59','loregrassi',106,'Io ti accompagno però il bagno non lo faccio'),(149,'2023-02-06 21:26:48','_anna_',107,'Nono lo stage non è obbligatorio'),(150,'2023-02-06 21:27:13','_anna_',92,'Io sono nuova a Pisa'),(151,'2023-02-06 21:33:55','loregrassi',100,'Giretto in centro e via, se vuoi scrivimi!'),(152,'2023-02-06 21:35:26','caterina',113,'In bocca al lupo!!'),(153,'2023-02-06 21:36:37','caterina',101,'Ma in gruppo?'),(154,'2023-02-06 21:38:10','alessio_2001',92,'Raga quando volete io sto a Pisa da poco e voglio fare nuove amicizie'),(155,'2023-02-06 21:43:57','DavideParisi',117,'Non per gufare, ma mi sa che te lo devi ricomprare'),(156,'2023-02-06 21:45:53','mirko.graffeo',117,'Forse l\'ho vistooo'),(157,'2023-02-06 21:49:12','singleboy',116,'ce n\'è una in via giunta pisano, io ci vado già da un po\' di tempo e mi trovo bene. costa 50 euro al mese e è ben attrezzata.'),(158,'2023-02-06 21:50:42','singleboy',103,'Sorry. I\'ve seen the first season. Are there any other?'),(159,'2023-02-06 21:53:09','davidecarraesi',118,'Yes it\'s so fun!!\r\nYou\'re gonna love Barney Stinson!'),(160,'2023-02-06 21:58:27','loregrassi',103,'Yes there\'s 9 seasons bro');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communities`
--

DROP TABLE IF EXISTS `communities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communities` (
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name_` varchar(30) NOT NULL,
  `creator` varchar(20) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`name_`),
  KEY `creator` (`creator`),
  CONSTRAINT `communities_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communities`
--

LOCK TABLES `communities` WRITE;
/*!40000 ALTER TABLE `communities` DISABLE KEYS */;
INSERT INTO `communities` VALUES ('2023-02-06 21:18:14','Campionato_di_calcio','DavideParisi','Per appassionati del calcio italiano e non'),('2023-02-06 20:37:32','formula_1','loregrassi','Per tutti gli appassionati di Formula 1 ?'),('2023-02-06 20:44:13','gruppi_musicali','andrea-g','Per chi canta o suona in un gruppo musicale'),('2023-02-06 21:40:42','harry_potter','giulia_corsetti02','Chi altro ama harry potter!!!!'),('2023-02-06 20:43:41','himym','loregrassi','Only for How I Met Your Mother fans'),('2023-02-06 20:38:55','ita-viaggi','loregrassi','Per gli italiani in cerca di qualcuno con cui viaggiare ✈️✈️'),('2023-02-06 20:36:57','lucca','loregrassi','Solo per i lucchesi bao'),('2023-02-06 20:40:37','new_york_city','loregrassi','For new yorkers ?'),('2023-02-06 20:36:01','pisa','loregrassi','Per tutti i pisani e i simpatizzanti!'),('2023-02-06 20:50:04','Rifondazione_partito_comunista','alessio_2001','Vieni anche tu a distribuire volantini davanti all\'università'),('2023-02-06 20:41:46','toscana-mare','loregrassi','Chi viene al Mare in toscana??'),('2023-02-06 20:53:44','traveling','shannonflynn','Looking for a travel buddy?'),('2023-02-06 20:41:19','uiuc','loregrassi','Students of the University of Illinois at Urbana-Champaign'),('2023-02-06 20:35:35','unibo','loregrassi','Community dell\'università di Bologna!'),('2023-02-06 20:33:01','unipi','loregrassi','Per gli sudenti dell\'Università di Pisa');
/*!40000 ALTER TABLE `communities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `post` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post` (`post`),
  KEY `likes_ibfk_1` (`username`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=270 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (204,'alessio_2001',91),(205,'alessio_2001',94),(206,'andrea-g',91),(207,'alessio_2001',96),(208,'singleboy',92),(209,'AriannaNardi',91),(210,'singleboy',91),(211,'AriannaNardi',96),(212,'shelbychurch',98),(213,'shannonflynn',98),(214,'leonardocagliostro',92),(215,'leonardocagliostro',100),(217,'alessio_2001',92),(218,'alessio_2001',100),(219,'JohnnyStecchino',101),(220,'fedeee',92),(221,'fedeee',94),(222,'fedeee',97),(223,'fedeee',101),(224,'loregrassi',103),(225,'nicola20',96),(226,'DavideParisi',100),(227,'samu__menchini',92),(228,'samu__menchini',91),(229,'samu__menchini',96),(230,'samu__menchini',97),(231,'brandonn',98),(232,'DavideParisi',94),(233,'brandonn',102),(234,'DavideParisi',92),(235,'shannonflynn',109),(236,'loregrassi',106),(237,'_anna_',97),(238,'_anna_',106),(239,'loregrassi',100),(240,'caterina',114),(241,'loregrassi',91),(242,'loregrassi',97),(243,'caterina',92),(244,'caterina',100),(245,'caterina',113),(246,'alessio_2001',114),(247,'caterina',101),(248,'caterina',117),(249,'alessio_2001',106),(250,'giulia_corsetti02',92),(251,'giulia_corsetti02',111),(252,'giulia_corsetti02',116),(253,'mirko.graffeo',91),(255,'mirko.graffeo',106),(256,'mirko.graffeo',117),(257,'nicole.massari17',92),(258,'singleboy',94),(259,'davidecarraesi',92),(260,'davidecarraesi',97),(261,'davidecarraesi',106),(262,'andrea-g',93),(263,'samu__menchini',119),(264,'loregrassi',112),(265,'loregrassi',120),(266,'loregrassi',107),(269,'loregrassi',92);
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(20) NOT NULL,
  `msg_text` text,
  `application` int(11) DEFAULT NULL,
  `isread` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `messages_ibfk_2` (`application`),
  KEY `messages_ibfk_1` (`username`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`application`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (182,'2023-02-06 21:22:37','alessio_2001','Ciao io ci sono, se vuoi scrivimi su insta',100,1),(183,'2023-02-06 21:21:38','shannonflynn','Hey if you really wanna come to New Zeland we can talk on Instagram',103,0),(184,'2023-02-06 21:22:01','shannonflynn','I\'d love to go maybe on March or something...',103,0),(185,'2023-02-06 21:22:42','loregrassi','Vai',100,0),(186,'2023-02-06 21:22:49','loregrassi','Poi semmai faccio un gruppo',100,0),(187,'2023-02-06 21:30:30','leonardocagliostro','Ciao se mi vuoi scrivere io ci sono',104,0),(188,'2023-02-06 21:30:40','leonardocagliostro','Anche per altri giorni non solo il weekend',104,0),(189,'2023-02-06 21:44:52','DavideParisi','ciao, hai già fatto la tesina?',120,0),(190,'2023-02-06 21:53:31','andrea-g','Hai seguito il corso quest\'anno?',107,0),(191,'2023-02-06 21:56:56','loregrassi','Ciaoo allora ti do un po\' di info',102,0),(192,'2023-02-06 21:57:12','loregrassi','Il volo parte da Roma con WizzAir e costa sui 100€',102,0),(193,'2023-02-06 21:57:20','loregrassi','Per Erevan',102,0),(194,'2023-02-06 21:57:40','loregrassi','Poi io pensavo di noleggiare un\'auto... fammi sapere se ti interessaaaaa',102,0),(195,'2023-02-06 22:10:39','loregrassi','Fra se vuoi ci si mette d\'accordo io penso di uscire, se ti vuoi unire volentierii',122,0);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_reports`
--

DROP TABLE IF EXISTS `post_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `post` int(11) NOT NULL,
  `report_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `denied` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `post` (`post`),
  CONSTRAINT `post_reports_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `post_reports_ibfk_2` FOREIGN KEY (`post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_reports`
--

LOCK TABLES `post_reports` WRITE;
/*!40000 ALTER TABLE `post_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(20) DEFAULT NULL,
  `content` text NOT NULL,
  `nlikes` int(11) NOT NULL DEFAULT '0',
  `community` varchar(30) DEFAULT NULL,
  `ncomments` int(11) NOT NULL DEFAULT '0',
  `anonym` tinyint(1) NOT NULL DEFAULT '0',
  `napplies` int(11) DEFAULT '0',
  `open` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `posts_ibfk_2` (`community`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`community`) REFERENCES `communities` (`name_`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (91,'2023-02-06 20:45:51','loregrassi','Ciao raga cerco qualcuno che vorrebbe venire in Armenia dal 5 al 9 aprile ??',7,'ita-viaggi',2,1,3,1),(92,'2023-02-06 20:46:36','loregrassi','Qualcuno per uscire questo weekend?',11,'unipi',5,1,7,1),(93,'2023-02-06 20:47:37','andrea-g','La mia rock band cerca urgentemente un bassista esperto. Facciamo prevalentemente cover dei Queen e dei Rolling Stones.',1,'gruppi_musicali',0,0,0,1),(94,'2023-02-06 20:48:20','andrea-g','Chi è interssato a metter su un fan club di Leclerc?',4,'formula_1',2,0,3,1),(95,'2023-02-06 20:50:34','alessio_2001','Chi viene all\'incontro Giovedì al Circolo Operaio?',0,'Rifondazione_partito_comunista',0,0,0,1),(96,'2023-02-06 20:50:48','andrea-g','Ho due biglietti del concerto di John Legend al Summer Festival. Qualcuno mi fa compagnia?',4,'lucca',1,0,3,1),(97,'2023-02-06 20:52:19','andrea-g','Cerco appunti del corso di Reti Logiche',5,'unipi',2,0,1,1),(98,'2023-02-06 20:54:10','shannonflynn','Looking for anyone who wants to go to New Zeland',3,'traveling',2,0,2,1),(99,'2023-02-06 20:57:39','AriannaNardi','Qualcuno che mi aiuta a passare Reti logiche?',0,'unibo',0,1,0,1),(100,'2023-02-06 20:57:41','singleboy','Ciao a tutti. Che fate il sabato sera per divertirvi a Pisa?',5,'pisa',3,0,3,1),(101,'2023-02-06 20:59:18','singleboy','Vacanze di Pasqua: che ne dite di una gita a Parigi?',3,'ita-viaggi',3,1,3,1),(102,'2023-02-06 21:01:14','shelbychurch','I\'m looking for a videographer to help me shoot this Saturday morning on the brooklyn bridge',1,'new_york_city',0,0,1,1),(103,'2023-02-06 21:01:25','singleboy','Io ho visto solo la prima stagione. Ma ce ne sono state altre?',1,'himym',3,0,0,1),(104,'2023-02-06 21:07:11','JohnnyStecchino','Per Anatomia 1 col prof. Ballantini cerco appunti e materiale su cui studiare. Ho intenzione di darlo a giugno: secondo voi ce la posso fare se parto da zero?',0,'unibo',0,0,0,1),(105,'2023-02-06 21:11:03','JohnnyStecchino','AIUTO!!! Devo lasciare l\'appartamento dove abito entro un mese, cerco posto letto in centro o vicino, max 400 euro al mese, anche in camera condivisa.',0,'unibo',0,0,0,1),(106,'2023-02-06 21:14:17','nicola20','Lo so che è inverno ma qualcuno vuole fare un tuffo questo weekend?',5,'toscana-mare',1,1,1,1),(107,'2023-02-06 21:16:42','DavideParisi','Qualcuno ha già fatto la tesina? In cosa consiste? Bisogna anche fare lo stage in un\'azienda?',1,'unipi',1,0,1,1),(108,'2023-02-06 21:19:27','DavideParisi','Raga, qualcuno di Pisa ha dazn per vedere le partite? Io sono tifoso del Milan...',0,'Campionato_di_calcio',0,0,0,1),(109,'2023-02-06 21:20:46','brandonn','Anyone wants to go out this tuesday?',1,'new_york_city',0,0,0,1),(110,'2023-02-06 21:26:34','DavideParisi','ATTENZIONE: vendo calcolatrice programmabile Sharp EL-231, completa di manuale, poco usata. 150 euro trattabili. Scrivetemi solo se interessati. NO PERDITEMPO.',0,'unipi',0,0,0,1),(111,'2023-02-06 21:29:35','_anna_','Vendo 2 biglietti del treno del 24 marzo 2023, PISA CENTRALE - ROMA TERMINI delle 14:50 che arriva alle 18.03',1,'unipi',0,0,1,1),(112,'2023-02-06 21:31:09','caterina','C\'è qualcuno che studia fisica nucleare? io non ci capisco nulla',1,'unipi',0,0,0,1),(113,'2023-02-06 21:32:58','leonardocagliostro','Ciao! Cerco la ragazza con maglioncino nero e cuffiette bianche che oggi (04/02) era seduta al tavolo davanti al mio, di fronte a me in aula studio al polo Piagge. Sono il ragazzo con il maglione nero e cappotto cammello. Ci siamo guardati un paio di volte, mi piacerebbe conoscerti!',1,'unipi',1,0,0,1),(114,'2023-02-06 21:33:56','caterina','Ho perso il cellulare sabato scorso in centro. E\' un Iphone 12 con cover rosa fuxia e il vetro ha una crepa in diagonale. Se qualcuno lo trovasse per favore mi contatti subito',2,'unipi',0,0,0,1),(115,'2023-02-06 21:36:12','alessio_2001','Urgenteeee\r\nHo perso le airpods a Pisa se qualcuno le trova mi contatti',0,'pisa',0,0,0,1),(116,'2023-02-06 21:37:28','alessio_2001','Spotto palestre convenienti a Pisa',1,'unipi',1,1,0,1),(117,'2023-02-06 21:37:37','caterina','Ho perso il cellulare sabato scorso in centro. E\' un Iphone 12 con cover rosa fuxia e il vetro ha una crepa in diagonale. Se qualcuno lo trovasse per favore mi contatti subito',2,'pisa',2,0,1,1),(118,'2023-02-06 21:40:15','caterina','I\'ve never seen it. Is it fun? Do you advise to whatch it?',0,'himym',1,0,0,1),(119,'2023-02-06 21:47:42','mirko.graffeo','Ciao, cerco qualcuno che faccia ripetizioni di chimica generale',1,'unipi',0,1,0,1),(120,'2023-02-06 21:50:52','nicole.massari17','Ciao, spotto ragazzo visto ieri (2 febbraio) al pl, capelli neri, orecchini a cerchio argentati, maglione verde e jeans :)',1,'unipi',0,1,0,1),(121,'2023-02-06 21:56:01','samu__menchini','Raga sono disperato per Anatomia... qualcuno che mi aiuta ?',0,'unipi',0,0,0,1);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `username` varchar(20) NOT NULL,
  `community` varchar(30) NOT NULL,
  `join_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`,`community`),
  KEY `community` (`community`),
  CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`community`) REFERENCES `communities` (`name_`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES ('alessio_2001','formula_1','2023-02-06 20:51:00'),('alessio_2001','ita-viaggi','2023-02-06 20:48:07'),('alessio_2001','lucca','2023-02-06 20:51:18'),('alessio_2001','pisa','2023-02-06 20:47:59'),('alessio_2001','Rifondazione_partito_comunista','2023-02-06 20:50:04'),('alessio_2001','unipi','2023-02-06 20:48:03'),('andrea-g','formula_1','2023-02-06 20:44:20'),('andrea-g','gruppi_musicali','2023-02-06 20:44:13'),('andrea-g','himym','2023-02-06 20:44:19'),('andrea-g','ita-viaggi','2023-02-06 20:44:22'),('andrea-g','lucca','2023-02-06 20:43:12'),('andrea-g','pisa','2023-02-06 20:43:09'),('andrea-g','uiuc','2023-02-06 20:43:11'),('andrea-g','unibo','2023-02-06 20:43:13'),('andrea-g','unipi','2023-02-06 20:44:20'),('AriannaNardi','himym','2023-02-06 20:57:48'),('AriannaNardi','ita-viaggi','2023-02-06 20:56:20'),('AriannaNardi','lucca','2023-02-06 20:56:17'),('AriannaNardi','unibo','2023-02-06 20:56:58'),('brandonn','himym','2023-02-06 21:19:36'),('brandonn','new_york_city','2023-02-06 21:19:20'),('brandonn','traveling','2023-02-06 21:19:29'),('brandonn','uiuc','2023-02-06 21:19:10'),('caterina','himym','2023-02-06 21:29:08'),('caterina','ita-viaggi','2023-02-06 21:28:56'),('caterina','pisa','2023-02-06 21:28:58'),('caterina','unipi','2023-02-06 21:28:55'),('davidecarraesi','himym','2023-02-06 21:52:03'),('davidecarraesi','lucca','2023-02-06 21:52:00'),('davidecarraesi','toscana-mare','2023-02-06 21:52:05'),('davidecarraesi','unipi','2023-02-06 21:51:58'),('DavideParisi','Campionato_di_calcio','2023-02-06 21:18:14'),('DavideParisi','formula_1','2023-02-06 21:19:47'),('DavideParisi','ita-viaggi','2023-02-06 21:13:18'),('DavideParisi','pisa','2023-02-06 21:13:22'),('DavideParisi','unipi','2023-02-06 21:13:20'),('fedeee','formula_1','2023-02-06 21:09:33'),('fedeee','ita-viaggi','2023-02-06 21:09:28'),('fedeee','pisa','2023-02-06 21:09:26'),('fedeee','unipi','2023-02-06 21:09:51'),('giulia_corsetti02','harry_potter','2023-02-06 21:40:42'),('giulia_corsetti02','pisa','2023-02-06 21:40:15'),('giulia_corsetti02','unipi','2023-02-06 21:40:12'),('JohnnyStecchino','ita-viaggi','2023-02-06 21:07:29'),('JohnnyStecchino','unibo','2023-02-06 21:04:48'),('leonardocagliostro','pisa','2023-02-06 21:05:30'),('leonardocagliostro','unipi','2023-02-06 21:05:36'),('loregrassi','formula_1','2023-02-06 20:46:07'),('loregrassi','himym','2023-02-06 20:43:41'),('loregrassi','ita-viaggi','2023-02-06 20:38:55'),('loregrassi','lucca','2023-02-06 20:36:57'),('loregrassi','pisa','2023-02-06 20:36:01'),('loregrassi','toscana-mare','2023-02-06 20:41:46'),('loregrassi','unipi','2023-02-06 20:33:01'),('mirko.graffeo','ita-viaggi','2023-02-06 21:44:51'),('mirko.graffeo','lucca','2023-02-06 21:44:53'),('mirko.graffeo','pisa','2023-02-06 21:44:51'),('mirko.graffeo','toscana-mare','2023-02-06 21:44:56'),('mirko.graffeo','unipi','2023-02-06 21:44:50'),('nicola20','lucca','2023-02-06 21:13:37'),('nicola20','toscana-mare','2023-02-06 21:13:43'),('nicole.massari17','pisa','2023-02-06 21:49:17'),('nicole.massari17','unipi','2023-02-06 21:49:16'),('samu__menchini','ita-viaggi','2023-02-06 21:17:12'),('samu__menchini','lucca','2023-02-06 21:17:18'),('samu__menchini','unipi','2023-02-06 21:17:16'),('shannonflynn','new_york_city','2023-02-06 20:54:37'),('shannonflynn','traveling','2023-02-06 20:53:44'),('shannonflynn','uiuc','2023-02-06 20:54:34'),('shelbychurch','himym','2023-02-06 21:03:41'),('shelbychurch','new_york_city','2023-02-06 20:59:52'),('shelbychurch','traveling','2023-02-06 21:00:00'),('singleboy','formula_1','2023-02-06 21:51:11'),('singleboy','himym','2023-02-06 21:00:50'),('singleboy','ita-viaggi','2023-02-06 20:54:53'),('singleboy','lucca','2023-02-06 20:54:41'),('singleboy','pisa','2023-02-06 20:54:39'),('singleboy','unipi','2023-02-06 20:54:43'),('_anna_','lucca','2023-02-06 21:26:08'),('_anna_','pisa','2023-02-06 21:26:13'),('_anna_','toscana-mare','2023-02-06 21:27:57'),('_anna_','unibo','2023-02-06 21:27:31'),('_anna_','unipi','2023-02-06 21:26:12');
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info` (
  `username` varchar(20) NOT NULL,
  `description` text,
  `instagram` varchar(30) DEFAULT NULL,
  `snapchat` varchar(15) DEFAULT NULL,
  `facebook` varchar(50) DEFAULT NULL,
  `favorite` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`username`),
  CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_info`
--

LOCK TABLES `user_info` WRITE;
/*!40000 ALTER TABLE `user_info` DISABLE KEYS */;
INSERT INTO `user_info` VALUES ('alessio_2001','Studente dell\'unipi :)','','','',NULL),('andrea-g','Studente universitario lucchese in cerca di occasioni','','','',NULL),('AriannaNardi','Ciaoo\r\nSono di Lucca ma studio ingegneria all\'unibo','arinardi02','','','instagram'),('brandonn','Heyy','brandonn33','','','instagram'),('caterina','Studentessa di fisica a Pisa','','','',NULL),('davidecarraesi','','davidecarraesi','','','instagram'),('DavideParisi','Studente al terzo anno di ingegneria informatica','','','',NULL),('fedeee','Studio ing info a unipi.\r\nAppassionato di Formula 1\r\nAmo viaggiare','fede_rossi','','federossi01','instagram'),('giulia_corsetti02','Pugliese all\'unipi\r\nHarry Potter fan','giulia_corsetti02','','','instagram'),('JohnnyStecchino','Sono uno studente fuori sede al primo anno di medicina. Sono di Brindisi, ma vivo e studio a Bologna.','','','',NULL),('leonardocagliostro','Poliziotto di Pisa','leocag97','','','instagram'),('loregrassi','Ciao raga ?','lorenzo_grassi__','','','instagram'),('mirko.graffeo','Pisa/Italy\r\nServizio civile Putignano/Pisa\r\nLa bellezza inizia nel momento in cui decici di essere te stesso','mirko.graffeo','','','instagram'),('nicola20','Faccio il bagnino in darsena a Viareggio','','nicolaa20','','snapchat'),('nicole.massari17','\"Tu hai un idea del perché un corvo somigli ad una scrivania?\"\r\nScienze biologiche @unipi','nicole.massari17','','','instagram'),('samu__menchini','Ciaooo\r\nSono di Lucca e studio medicina all\'unipi','samu_menchini2','','','instagram'),('shannonflynn','Hey there! I\'m a freshman at UIUC.\r\nOriginally from New York','','','shannonflynn','facebook'),('shelbychurch','Hey! I\'m a Youtuber\r\nI\'m from Seattle but I live in New York','shelbychurch','','','instagram'),('singleboy','Giovane pisano single','','','',NULL),('_anna_','?','_anna_lucchesi_','','','instagram');
/*!40000 ALTER TABLE `user_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `username` varchar(20) NOT NULL,
  `pword` char(60) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('alessio_2001','$2y$10$B6Y1O90rc7WWX/.Fqtlcvux1dM1Lu13oAvLO3gvGtMlRhBUyws3/.'),('andrea-g','$2y$10$fBTZQokEIT6iQWKX6ecbZedRqPgSsKlhANog99zXt8qd80w4vht66'),('AriannaNardi','$2y$10$sRh1jAQZghjM1JtI65U.5.9hzFcRgKJ7YBkWnE5lpEUpK9A9tKGba'),('brandonn','$2y$10$29IYUEEnQ3AKBefFa/5EAe7MiHGGbs6m7NJAZDly3DyBEaeXqqyC6'),('caterina','$2y$10$wusftAVrXEYKqyQUKV/y7.PMAZrH1.0LCfqOcGep9puvngk5MLzK6'),('davidecarraesi','$2y$10$GC8zJx9w05WFj/W3Lji0kenjbddsb445F3OXSzIoZBiNsnAeMdwDO'),('DavideParisi','$2y$10$QW.X5z.E.El2qc.hudobsOzjlvjVdrnaMwVh62tK6SPTjBESsbl1u'),('fedeee','$2y$10$sbmvK56wtUMWsWF3W.PIUOrN5CDnFiJzfHXTaFa9wYTArOxMD33fu'),('giulia_corsetti02','$2y$10$59yGQa3QPN1De8o4ZEkPC..5ixOhkqTJOlVglzoDU6Qz/B8TOcbci'),('JohnnyStecchino','$2y$10$NU9adMwVKZDgQY6nWkyCy.oIXbHOWw37qmIyiIKAouPW0bpNorqOS'),('leonardocagliostro','$2y$10$FdwnWzIPZt7CeXYQ5iO6uupdL2tK8jdEb7uBBd7e6Daq40T1zO3EW'),('loregrassi','$2y$10$/8b2/5M2GjDYYt0r0xfsXew.UjLfD2KEF1LLeO7j0XpljNVrEWxZe'),('mirko.graffeo','$2y$10$uJA8ftsuYEcdqNRqfOtg9O6FnBYnbb0vUVUC9CsrvZSzqk4phKpd6'),('nicola20','$2y$10$0uU1KD3o0vaPN4/4JGUj.ehAiQ1Kl4UBnunj9VSJ6RaaMg5L63v42'),('nicole.massari17','$2y$10$QTEOy6qjal54ag7umVTALuFigk/ZaEV5KZNL3YQcc5dX3xPnedGDu'),('samu__menchini','$2y$10$tHtEtkuwp9j3jXm8wJ.v/OXajpq9ZcUnl4JV7AusQhD9b/tuPyABe'),('shannonflynn','$2y$10$jO59Y1Ut3/Atsu8UyzTYBefP.oqLXPgJMI58LucjvaO/5Kk6alElu'),('shelbychurch','$2y$10$Kg3/5RHVugs5u3PrURG3l.sAENabFsJEJkuoK4HzSqbvJhzT/kQV6'),('singleboy','$2y$10$e.xq8ZZJXCKVWkgCp3ktV.O.LEGEHtYURunrVYPUyGMc97uWBxjbS'),('_anna_','$2y$10$yHZDLMPVPGHHp3f.KoqjUOdBPjuXVcb.D.8ouqIt/8vvn9LwsjGmK');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-02-06 23:11:29
