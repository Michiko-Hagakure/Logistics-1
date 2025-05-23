/*
SQLyog Community v13.2.0 (64 bit)
MySQL - 8.0.32 : Database - logs1_project_management
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`logs1_project_management` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `logs1_project_management`;

/*Table structure for table `milestones` */

DROP TABLE IF EXISTS `milestones`;

CREATE TABLE `milestones` (
  `milestoneID` int NOT NULL AUTO_INCREMENT,
  `projectID` int NOT NULL,
  `taskName` varchar(255) NOT NULL,
  `milestoneName` varchar(255) NOT NULL,
  `dueDate` date NOT NULL,
  `status` enum('Not Started','In Progress','Achieved') NOT NULL DEFAULT 'Not Started',
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`milestoneID`),
  UNIQUE KEY `taskName` (`taskName`,`milestoneName`),
  UNIQUE KEY `taskName_2` (`taskName`,`milestoneName`),
  KEY `projectID` (`projectID`),
  CONSTRAINT `milestones_ibfk_1` FOREIGN KEY (`projectID`) REFERENCES `projects` (`projectID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `milestones` */

insert  into `milestones`(`milestoneID`,`projectID`,`taskName`,`milestoneName`,`dueDate`,`status`,`createdAt`,`updatedAt`) values 
(1,1,'Fleet Coordination','Project Kickoff','2025-06-25','In Progress','2025-05-15 00:38:01','2025-05-18 18:31:13'),
(2,2,'Cargo Tracking Setup','Prototype Development','2025-06-22','Not Started','2025-05-15 00:38:32','2025-05-18 18:37:01'),
(3,3,'Supply Chain Monitoring','Quality Assurance Passed','2025-05-20','Achieved','2025-05-15 00:38:56','2025-05-18 18:27:08');

/*Table structure for table `projects` */

DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `projectID` int NOT NULL AUTO_INCREMENT,
  `projectName` varchar(255) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `status` enum('pending','decline','approve') NOT NULL DEFAULT 'pending',
  `budget` decimal(12,2) DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`projectID`),
  UNIQUE KEY `projectName` (`projectName`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `projects` */

insert  into `projects`(`projectID`,`projectName`,`startDate`,`endDate`,`status`,`budget`,`createdAt`,`updatedAt`) values 
(1,'Logistics Smart','2025-05-15','2025-05-17','pending',3500000.00,'2025-05-13 23:11:04','2025-05-18 17:45:26'),
(2,'Logistics 2','2025-05-15','2025-05-25','pending',2453453.00,'2025-05-14 13:51:22','2025-05-18 17:29:15'),
(3,'Warehouse Management','2025-05-15','2025-05-20','pending',2500000.00,'2025-05-15 00:28:21','2025-05-18 17:29:15'),
(4,'Fleet Management','2025-05-15','2025-05-25','pending',3500000.00,'2025-05-15 00:28:52','2025-05-18 17:29:15'),
(5,'Order Fulfillment','2025-05-16','2025-05-28','pending',2000000.00,'2025-05-15 00:29:12','2025-05-18 17:29:15'),
(6,'Logistics Data Analytics','2025-05-27','2025-05-29','pending',1200000.00,'2025-05-15 00:29:39','2025-05-18 17:29:15'),
(7,'Document Tracking','2025-06-20','2025-07-20','pending',450000.00,'2025-05-16 00:07:01','2025-05-18 17:29:15'),
(9,'Logistics Smart 2','2025-05-18','2025-05-30','pending',1200000.00,'2025-05-18 17:52:51','2025-05-18 17:53:02'),
(10,'FleetSync','2025-05-18','2025-06-01','pending',300000.00,'2025-05-18 18:04:47','2025-05-18 18:05:01');

/*Table structure for table `projecttask` */

DROP TABLE IF EXISTS `projecttask`;

CREATE TABLE `projecttask` (
  `taskID` int NOT NULL AUTO_INCREMENT,
  `projectID` int NOT NULL DEFAULT '1',
  `taskName` varchar(255) NOT NULL,
  `employeeName` varchar(255) NOT NULL,
  `startDate` date NOT NULL,
  `dueDate` date NOT NULL,
  `status` enum('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending',
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`taskID`),
  UNIQUE KEY `taskName` (`taskName`,`employeeName`),
  KEY `projectID` (`projectID`),
  CONSTRAINT `projecttask_ibfk_1` FOREIGN KEY (`projectID`) REFERENCES `projects` (`projectID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `projecttask` */

insert  into `projecttask`(`taskID`,`projectID`,`taskName`,`employeeName`,`startDate`,`dueDate`,`status`,`createdAt`,`updatedAt`) values 
(1,1,'Document Tracks','Shen Yuan','2025-06-01','2025-07-01','Pending','2025-05-16 02:23:02','2025-05-18 18:10:02'),
(5,1,'Inventory Check','Daiki Kobayashi','2025-05-18','2025-06-05','Pending','2025-05-18 18:05:58','2025-05-18 18:05:58'),
(6,1,'Work Hours Recording','Mika Watanabe','2025-05-20','2025-06-18','Pending','2025-05-18 18:10:37','2025-05-18 18:13:19');

/*Table structure for table `timesheets` */

DROP TABLE IF EXISTS `timesheets`;

CREATE TABLE `timesheets` (
  `timesheetID` int NOT NULL AUTO_INCREMENT,
  `employeeName` varchar(255) NOT NULL,
  `taskName` varchar(255) NOT NULL,
  `projectID` int NOT NULL DEFAULT '1',
  `workDate` date NOT NULL,
  `hoursWorked` decimal(5,2) NOT NULL,
  `description` text,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`timesheetID`),
  UNIQUE KEY `employeeName` (`employeeName`,`taskName`,`workDate`),
  UNIQUE KEY `employeeName_2` (`employeeName`,`taskName`,`workDate`),
  UNIQUE KEY `employeeName_3` (`employeeName`,`taskName`,`workDate`),
  KEY `projectID` (`projectID`),
  CONSTRAINT `timesheets_ibfk_1` FOREIGN KEY (`projectID`) REFERENCES `projects` (`projectID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `timesheets` */

insert  into `timesheets`(`timesheetID`,`employeeName`,`taskName`,`projectID`,`workDate`,`hoursWorked`,`description`,`createdAt`,`updatedAt`) values 
(1,'Cheng Ming','Fleet Coordination',1,'2025-06-29',5.00,'Fixed bugs and tested new features for the application','2025-05-15 00:44:13','2025-05-15 23:37:51'),
(2,'Jiwon Lee','Supply Chain Monitoring',2,'2025-06-12',7.00,'Wrote unit tests for newly developed features.','2025-05-15 00:44:50','2025-05-15 00:44:59'),
(3,'Hiroshi Takahashi','Cargo Tracking',3,'2025-05-15',6.00,'Investigated reported issue and provided solutions','2025-05-15 00:45:52','2025-05-16 00:00:10');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
