CREATE TABLE `certificates` (
	`id` int AUTO_INCREMENT NOT NULL,
	`userId` int NOT NULL,
	`competitionId` int NOT NULL,
	`certificateNumber` varchar(100) NOT NULL,
	`type` enum('participation','winner','runner_up','excellence') DEFAULT 'participation',
	`issuedAt` timestamp NOT NULL DEFAULT (now()),
	`certificateUrl` text,
	CONSTRAINT `certificates_id` PRIMARY KEY(`id`),
	CONSTRAINT `certificates_certificateNumber_unique` UNIQUE(`certificateNumber`)
);
--> statement-breakpoint
CREATE TABLE `competition_registrations` (
	`id` int AUTO_INCREMENT NOT NULL,
	`competitionId` int NOT NULL,
	`userId` int NOT NULL,
	`schoolId` int,
	`status` enum('pending','approved','rejected','withdrawn') DEFAULT 'pending',
	`registeredAt` timestamp NOT NULL DEFAULT (now()),
	`approvedAt` timestamp,
	`notes` text,
	CONSTRAINT `competition_registrations_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `competitions` (
	`id` int AUTO_INCREMENT NOT NULL,
	`title` varchar(200) NOT NULL,
	`slug` varchar(200) NOT NULL,
	`description` text,
	`shortDescription` varchar(500),
	`category` enum('arabic','english','math','science','general','reading','other') NOT NULL,
	`educationLevel` enum('primary','middle','secondary','university','all') DEFAULT 'all',
	`ageFrom` int,
	`ageTo` int,
	`startDate` timestamp,
	`endDate` timestamp,
	`registrationDeadline` timestamp,
	`maxParticipants` int,
	`status` enum('upcoming','active','completed','cancelled') DEFAULT 'upcoming',
	`imageUrl` text,
	`rules` text,
	`prizes` text,
	`stages` text,
	`isFeatured` boolean DEFAULT false,
	`createdBy` int,
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	`updatedAt` timestamp NOT NULL DEFAULT (now()) ON UPDATE CURRENT_TIMESTAMP,
	CONSTRAINT `competitions_id` PRIMARY KEY(`id`),
	CONSTRAINT `competitions_slug_unique` UNIQUE(`slug`)
);
--> statement-breakpoint
CREATE TABLE `contact_messages` (
	`id` int AUTO_INCREMENT NOT NULL,
	`name` varchar(100) NOT NULL,
	`email` varchar(320) NOT NULL,
	`phone` varchar(20),
	`subject` varchar(200),
	`message` text NOT NULL,
	`status` enum('new','read','replied') DEFAULT 'new',
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `contact_messages_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `newsletter_subscribers` (
	`id` int AUTO_INCREMENT NOT NULL,
	`email` varchar(320) NOT NULL,
	`name` varchar(100),
	`isActive` boolean DEFAULT true,
	`subscribedAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `newsletter_subscribers_id` PRIMARY KEY(`id`),
	CONSTRAINT `newsletter_subscribers_email_unique` UNIQUE(`email`)
);
--> statement-breakpoint
CREATE TABLE `partners` (
	`id` int AUTO_INCREMENT NOT NULL,
	`name` varchar(200) NOT NULL,
	`logoUrl` text,
	`websiteUrl` text,
	`type` enum('sponsor','educational','media','government') DEFAULT 'sponsor',
	`isActive` boolean DEFAULT true,
	`sortOrder` int DEFAULT 0,
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `partners_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `prizes` (
	`id` int AUTO_INCREMENT NOT NULL,
	`competitionId` int NOT NULL,
	`rank` int NOT NULL,
	`title` varchar(200) NOT NULL,
	`description` text,
	`value` decimal(10,2),
	`currency` varchar(10) DEFAULT 'SAR',
	`imageUrl` text,
	CONSTRAINT `prizes_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `results` (
	`id` int AUTO_INCREMENT NOT NULL,
	`competitionId` int NOT NULL,
	`userId` int NOT NULL,
	`score` decimal(10,2),
	`rank` int,
	`stage` varchar(50),
	`status` enum('qualified','eliminated','winner','runner_up'),
	`notes` text,
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `results_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `schools` (
	`id` int AUTO_INCREMENT NOT NULL,
	`userId` int NOT NULL,
	`schoolName` varchar(200) NOT NULL,
	`schoolCode` varchar(50),
	`city` varchar(100),
	`region` varchar(100),
	`educationType` enum('government','private','international') DEFAULT 'government',
	`principalName` varchar(100),
	`phone` varchar(20),
	`address` text,
	`isVerified` boolean DEFAULT false,
	`coordinatorId` int,
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `schools_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `student_profiles` (
	`id` int AUTO_INCREMENT NOT NULL,
	`userId` int NOT NULL,
	`studentId` varchar(50),
	`schoolId` int,
	`grade` varchar(50),
	`educationLevel` enum('primary','middle','secondary','university'),
	`dateOfBirth` date,
	`city` varchar(100),
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `student_profiles_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
CREATE TABLE `teacher_profiles` (
	`id` int AUTO_INCREMENT NOT NULL,
	`userId` int NOT NULL,
	`schoolId` int,
	`subject` varchar(100),
	`city` varchar(100),
	`createdAt` timestamp NOT NULL DEFAULT (now()),
	CONSTRAINT `teacher_profiles_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
ALTER TABLE `users` MODIFY COLUMN `role` enum('student','teacher','school','coordinator','admin','user') NOT NULL DEFAULT 'user';--> statement-breakpoint
ALTER TABLE `users` ADD `phone` varchar(20);--> statement-breakpoint
ALTER TABLE `users` ADD `avatarUrl` text;--> statement-breakpoint
ALTER TABLE `users` ADD `isActive` boolean DEFAULT true NOT NULL;