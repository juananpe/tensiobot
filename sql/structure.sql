use tensiobot;
--
-- Host: 127.0.0.1    Database: tensiobot
-- ------------------------------------------------------
-- Server version	5.7.15

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
-- Table structure for table `alertas`
--

DROP TABLE IF EXISTS `alertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alertas` (
  `user_id` bigint(20) NOT NULL,
  `hora1` datetime DEFAULT NULL,
  `hora2` datetime DEFAULT NULL,
  `iniciar` date DEFAULT NULL,
  `finalizar` date DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alertas`
--

LOCK TABLES `alertas` WRITE;
/*!40000 ALTER TABLE `alertas` DISABLE KEYS */;
INSERT INTO `alertas` VALUES (4694560,'2017-05-01 14:15:00','2017-05-01 22:55:00','2017-05-03','2017-05-30');
/*!40000 ALTER TABLE `alertas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `botan_shortener`
--

DROP TABLE IF EXISTS `botan_shortener`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `botan_shortener` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `url` text COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Original URL',
  `short_url` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'Shortened URL',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `botan_shortener_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `botan_shortener`
--

LOCK TABLES `botan_shortener` WRITE;
/*!40000 ALTER TABLE `botan_shortener` DISABLE KEYS */;
/*!40000 ALTER TABLE `botan_shortener` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callback_query`
--

DROP TABLE IF EXISTS `callback_query`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callback_query` (
  `id` bigint(20) unsigned NOT NULL COMMENT 'Unique identifier for this query',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
  `message_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Unique message identifier',
  `inline_message_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Identifier of the message sent via the bot in inline mode, that originated the query',
  `data` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'Data associated with the callback button',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `chat_id` (`chat_id`),
  KEY `message_id` (`message_id`),
  KEY `chat_id_2` (`chat_id`,`message_id`),
  CONSTRAINT `callback_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `callback_query_ibfk_2` FOREIGN KEY (`chat_id`, `message_id`) REFERENCES `message` (`chat_id`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callback_query`
--

LOCK TABLES `callback_query` WRITE;
/*!40000 ALTER TABLE `callback_query` DISABLE KEYS */;
/*!40000 ALTER TABLE `callback_query` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat` (
  `id` bigint(20) NOT NULL COMMENT 'Unique user or chat identifier',
  `type` enum('private','group','supergroup','channel') COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Chat type, either private, group, supergroup or channel',
  `title` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT '' COMMENT 'Chat (group) title, is null if chat type is private',
  `username` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Username, for private chats, supergroups and channels if available',
  `all_members_are_administrators` tinyint(1) DEFAULT '0' COMMENT 'True if a all members of this group are admins',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update',
  `old_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier, this is filled when a group is converted to a supergroup',
  PRIMARY KEY (`id`),
  KEY `old_id` (`old_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat`
--

LOCK TABLES `chat` WRITE;
/*!40000 ALTER TABLE `chat` DISABLE KEYS */;
INSERT INTO `chat` VALUES (4694560,'private',NULL,'juananpe',NULL,'2017-05-03 09:50:41','2017-05-03 21:10:50',NULL);
/*!40000 ALTER TABLE `chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chosen_inline_result`
--

DROP TABLE IF EXISTS `chosen_inline_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chosen_inline_result` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry',
  `result_id` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'Identifier for this result',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `location` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Location object, user''s location',
  `inline_message_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Identifier of the sent inline message',
  `query` text COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'The query that was used to obtain the result',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `chosen_inline_result_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chosen_inline_result`
--

LOCK TABLES `chosen_inline_result` WRITE;
/*!40000 ALTER TABLE `chosen_inline_result` DISABLE KEYS */;
/*!40000 ALTER TABLE `chosen_inline_result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversation`
--

DROP TABLE IF EXISTS `conversation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique user or chat identifier',
  `status` enum('active','cancelled','stopped') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'active' COMMENT 'Conversation state',
  `command` varchar(160) COLLATE utf8mb4_unicode_520_ci DEFAULT '' COMMENT 'Default command to execute',
  `notes` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Data stored from command',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `chat_id` (`chat_id`),
  KEY `status` (`status`),
  CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `conversation_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversation`
--

LOCK TABLES `conversation` WRITE;
/*!40000 ALTER TABLE `conversation` DISABLE KEYS */;
INSERT INTO `conversation` VALUES (1,4694560,4694560,'stopped','tomar','{\"state\":5,\"sistolica1\":\"148\",\"diastolica1\":\"98\",\"sistolica2\":\"149\",\"diastolica2\":\"99\"}','2017-05-03 10:56:25','2017-05-03 21:07:54');
/*!40000 ALTER TABLE `conversation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `edited_message`
--

DROP TABLE IF EXISTS `edited_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edited_message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry',
  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
  `message_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Unique message identifier',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `edit_date` timestamp NULL DEFAULT NULL COMMENT 'Date the message was edited in timestamp format',
  `text` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For text messages, the actual UTF-8 text of the message max message length 4096 char utf8',
  `entities` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text',
  `caption` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For message with caption, the actual UTF-8 text of the caption',
  PRIMARY KEY (`id`),
  KEY `chat_id` (`chat_id`),
  KEY `message_id` (`message_id`),
  KEY `user_id` (`user_id`),
  KEY `chat_id_2` (`chat_id`,`message_id`),
  CONSTRAINT `edited_message_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`),
  CONSTRAINT `edited_message_ibfk_2` FOREIGN KEY (`chat_id`, `message_id`) REFERENCES `message` (`chat_id`, `id`),
  CONSTRAINT `edited_message_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edited_message`
--

LOCK TABLES `edited_message` WRITE;
/*!40000 ALTER TABLE `edited_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `edited_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inline_query`
--

DROP TABLE IF EXISTS `inline_query`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inline_query` (
  `id` bigint(20) unsigned NOT NULL COMMENT 'Unique identifier for this query',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `location` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Location of the user',
  `query` text COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Text of the query',
  `offset` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Offset of the result',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `inline_query_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inline_query`
--

LOCK TABLES `inline_query` WRITE;
/*!40000 ALTER TABLE `inline_query` DISABLE KEYS */;
/*!40000 ALTER TABLE `inline_query` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `chat_id` bigint(20) NOT NULL COMMENT 'Unique chat identifier',
  `id` bigint(20) unsigned NOT NULL COMMENT 'Unique message identifier',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
  `date` timestamp NULL DEFAULT NULL COMMENT 'Date the message was sent in timestamp format',
  `forward_from` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier, sender of the original message',
  `forward_from_chat` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier, chat the original message belongs to',
  `forward_from_message_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier of the original message in the channel',
  `forward_date` timestamp NULL DEFAULT NULL COMMENT 'date the original message was sent in timestamp format',
  `reply_to_chat` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
  `reply_to_message` bigint(20) unsigned DEFAULT NULL COMMENT 'Message that this message is reply to',
  `text` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For text messages, the actual UTF-8 text of the message max message length 4096 char utf8mb4',
  `entities` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For text messages, special entities like usernames, URLs, bot commands, etc. that appear in the text',
  `audio` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Audio object. Message is an audio file, information about the file',
  `document` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Document object. Message is a general file, information about the file',
  `photo` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Array of PhotoSize objects. Message is a photo, available sizes of the photo',
  `sticker` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Sticker object. Message is a sticker, information about the sticker',
  `video` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Video object. Message is a video, information about the video',
  `voice` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Voice Object. Message is a Voice, information about the Voice',
  `contact` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Contact object. Message is a shared contact, information about the contact',
  `location` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Location object. Message is a shared location, information about the location',
  `venue` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Venue object. Message is a Venue, information about the Venue',
  `caption` text COLLATE utf8mb4_unicode_520_ci COMMENT 'For message with caption, the actual UTF-8 text of the caption',
  `new_chat_member` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier, a new member was added to the group, information about them (this member may be bot itself)',
  `left_chat_member` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier, a member was removed from the group, information about them (this member may be bot itself)',
  `new_chat_title` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'A chat title was changed to this value',
  `new_chat_photo` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Array of PhotoSize objects. A chat photo was change to this value',
  `delete_chat_photo` tinyint(1) DEFAULT '0' COMMENT 'Informs that the chat photo was deleted',
  `group_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the group has been created',
  `supergroup_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the supergroup has been created',
  `channel_chat_created` tinyint(1) DEFAULT '0' COMMENT 'Informs that the channel chat has been created',
  `migrate_to_chat_id` bigint(20) DEFAULT NULL COMMENT 'Migrate to chat identifier. The group has been migrated to a supergroup with the specified identifie',
  `migrate_from_chat_id` bigint(20) DEFAULT NULL COMMENT 'Migrate from chat identifier. The supergroup has been migrated from a group with the specified identifier',
  `pinned_message` text COLLATE utf8mb4_unicode_520_ci COMMENT 'Message object. Specified message was pinned',
  PRIMARY KEY (`chat_id`,`id`),
  KEY `user_id` (`user_id`),
  KEY `forward_from` (`forward_from`),
  KEY `forward_from_chat` (`forward_from_chat`),
  KEY `reply_to_chat` (`reply_to_chat`),
  KEY `reply_to_message` (`reply_to_message`),
  KEY `new_chat_member` (`new_chat_member`),
  KEY `left_chat_member` (`left_chat_member`),
  KEY `migrate_from_chat_id` (`migrate_from_chat_id`),
  KEY `migrate_to_chat_id` (`migrate_to_chat_id`),
  KEY `reply_to_chat_2` (`reply_to_chat`,`reply_to_message`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`),
  CONSTRAINT `message_ibfk_3` FOREIGN KEY (`forward_from`) REFERENCES `user` (`id`),
  CONSTRAINT `message_ibfk_4` FOREIGN KEY (`forward_from_chat`) REFERENCES `chat` (`id`),
  CONSTRAINT `message_ibfk_5` FOREIGN KEY (`reply_to_chat`, `reply_to_message`) REFERENCES `message` (`chat_id`, `id`),
  CONSTRAINT `message_ibfk_6` FOREIGN KEY (`forward_from`) REFERENCES `user` (`id`),
  CONSTRAINT `message_ibfk_7` FOREIGN KEY (`new_chat_member`) REFERENCES `user` (`id`),
  CONSTRAINT `message_ibfk_8` FOREIGN KEY (`left_chat_member`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (4694560,75,4694560,'2017-05-03 09:50:41',NULL,NULL,NULL,NULL,NULL,NULL,'/help','[{\"type\":\"bot_command\",\"offset\":0,\"length\":5}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,77,4694560,'2017-05-03 09:53:06',NULL,NULL,NULL,NULL,NULL,NULL,'/whoami','[{\"type\":\"bot_command\",\"offset\":0,\"length\":7}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,79,4694560,'2017-05-03 09:54:13',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,81,4694560,'2017-05-03 10:01:42',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,82,4694560,'2017-05-03 10:02:56',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,84,4694560,'2017-05-03 10:03:22',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,86,4694560,'2017-05-03 10:12:37',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,87,4694560,'2017-05-03 10:14:27',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,88,4694560,'2017-05-03 10:16:16',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,89,4694560,'2017-05-03 10:17:08',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,90,4694560,'2017-05-03 10:23:59',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,91,4694560,'2017-05-03 10:24:46',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,92,4694560,'2017-05-03 10:25:05',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,93,4694560,'2017-05-03 10:26:28',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,94,4694560,'2017-05-03 10:27:48',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,95,4694560,'2017-05-03 10:28:23',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,96,4694560,'2017-05-03 10:29:15',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,97,4694560,'2017-05-03 10:30:27',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,98,4694560,'2017-05-03 10:31:16',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,99,4694560,'2017-05-03 10:47:26',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,100,4694560,'2017-05-03 10:52:04',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,101,4694560,'2017-05-03 10:52:32',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,102,4694560,'2017-05-03 10:53:46',NULL,NULL,NULL,NULL,NULL,NULL,'/consultar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":10}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,104,4694560,'2017-05-03 10:56:15',NULL,NULL,NULL,NULL,NULL,NULL,'/help','[{\"type\":\"bot_command\",\"offset\":0,\"length\":5}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,106,4694560,'2017-05-03 10:56:25',NULL,NULL,NULL,NULL,NULL,NULL,'/tomar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":6}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,109,4694560,'2017-05-03 20:41:07',NULL,NULL,NULL,NULL,NULL,NULL,'/tomar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":6}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,111,4694560,'2017-05-03 20:41:18',NULL,NULL,NULL,NULL,NULL,NULL,'Sí, ahora',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,114,4694560,'2017-05-03 21:02:52',NULL,NULL,NULL,NULL,NULL,NULL,'/tomar','[{\"type\":\"bot_command\",\"offset\":0,\"length\":6}]',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,116,4694560,'2017-05-03 21:03:22',NULL,NULL,NULL,NULL,NULL,NULL,'140',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,117,4694560,'2017-05-03 21:03:56',NULL,NULL,NULL,NULL,NULL,NULL,'140',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,118,4694560,'2017-05-03 21:04:44',NULL,NULL,NULL,NULL,NULL,NULL,'140',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,119,4694560,'2017-05-03 21:06:21',NULL,NULL,NULL,NULL,NULL,NULL,'140',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,120,4694560,'2017-05-03 21:07:16',NULL,NULL,NULL,NULL,NULL,NULL,'148',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,122,4694560,'2017-05-03 21:07:28',NULL,NULL,NULL,NULL,NULL,NULL,'98',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,124,4694560,'2017-05-03 21:07:35',NULL,NULL,NULL,NULL,NULL,NULL,'OK, ya he vuelto a tomarme la tensión',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,126,4694560,'2017-05-03 21:07:43',NULL,NULL,NULL,NULL,NULL,NULL,'149',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,128,4694560,'2017-05-03 21:07:49',NULL,NULL,NULL,NULL,NULL,NULL,'99',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,131,4694560,'2017-05-03 21:08:02',NULL,NULL,NULL,NULL,NULL,NULL,'Consultar historial',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,132,4694560,'2017-05-03 21:09:26',NULL,NULL,NULL,NULL,NULL,NULL,'Consultar historial',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4694560,133,4694560,'2017-05-03 21:10:50',NULL,NULL,NULL,NULL,NULL,NULL,'Consultar historial',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_limiter`
--

DROP TABLE IF EXISTS `request_limiter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_limiter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for this entry',
  `chat_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Unique chat identifier',
  `inline_message_id` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Identifier of the sent inline message',
  `method` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'Request method',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_limiter`
--

LOCK TABLES `request_limiter` WRITE;
/*!40000 ALTER TABLE `request_limiter` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_limiter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telegram_update`
--

DROP TABLE IF EXISTS `telegram_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telegram_update` (
  `id` bigint(20) unsigned NOT NULL COMMENT 'Update''s unique identifier',
  `chat_id` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifier',
  `message_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Unique message identifier',
  `inline_query_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Unique inline query identifier',
  `chosen_inline_result_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Local chosen inline result identifier',
  `callback_query_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Unique callback query identifier',
  `edited_message_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Local edited message identifier',
  PRIMARY KEY (`id`),
  KEY `message_id` (`chat_id`,`message_id`),
  KEY `inline_query_id` (`inline_query_id`),
  KEY `chosen_inline_result_id` (`chosen_inline_result_id`),
  KEY `callback_query_id` (`callback_query_id`),
  KEY `edited_message_id` (`edited_message_id`),
  CONSTRAINT `telegram_update_ibfk_1` FOREIGN KEY (`chat_id`, `message_id`) REFERENCES `message` (`chat_id`, `id`),
  CONSTRAINT `telegram_update_ibfk_2` FOREIGN KEY (`inline_query_id`) REFERENCES `inline_query` (`id`),
  CONSTRAINT `telegram_update_ibfk_3` FOREIGN KEY (`chosen_inline_result_id`) REFERENCES `chosen_inline_result` (`id`),
  CONSTRAINT `telegram_update_ibfk_4` FOREIGN KEY (`callback_query_id`) REFERENCES `callback_query` (`id`),
  CONSTRAINT `telegram_update_ibfk_5` FOREIGN KEY (`edited_message_id`) REFERENCES `edited_message` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telegram_update`
--

LOCK TABLES `telegram_update` WRITE;
/*!40000 ALTER TABLE `telegram_update` DISABLE KEYS */;
INSERT INTO `telegram_update` VALUES (451280440,4694560,75,NULL,NULL,NULL,NULL),(451280441,4694560,77,NULL,NULL,NULL,NULL),(451280442,4694560,79,NULL,NULL,NULL,NULL),(451280443,4694560,81,NULL,NULL,NULL,NULL),(451280444,4694560,82,NULL,NULL,NULL,NULL),(451280445,4694560,84,NULL,NULL,NULL,NULL),(451280446,4694560,86,NULL,NULL,NULL,NULL),(451280447,4694560,87,NULL,NULL,NULL,NULL),(451280448,4694560,88,NULL,NULL,NULL,NULL),(451280449,4694560,89,NULL,NULL,NULL,NULL),(451280450,4694560,90,NULL,NULL,NULL,NULL),(451280451,4694560,91,NULL,NULL,NULL,NULL),(451280452,4694560,92,NULL,NULL,NULL,NULL),(451280453,4694560,93,NULL,NULL,NULL,NULL),(451280454,4694560,94,NULL,NULL,NULL,NULL),(451280455,4694560,95,NULL,NULL,NULL,NULL),(451280456,4694560,96,NULL,NULL,NULL,NULL),(451280457,4694560,97,NULL,NULL,NULL,NULL),(451280458,4694560,98,NULL,NULL,NULL,NULL),(451280459,4694560,99,NULL,NULL,NULL,NULL),(451280460,4694560,100,NULL,NULL,NULL,NULL),(451280461,4694560,101,NULL,NULL,NULL,NULL),(451280462,4694560,102,NULL,NULL,NULL,NULL),(451280463,4694560,104,NULL,NULL,NULL,NULL),(451280464,4694560,106,NULL,NULL,NULL,NULL),(451280465,4694560,109,NULL,NULL,NULL,NULL),(451280466,4694560,111,NULL,NULL,NULL,NULL),(451280467,4694560,114,NULL,NULL,NULL,NULL),(451280468,4694560,116,NULL,NULL,NULL,NULL),(451280469,4694560,117,NULL,NULL,NULL,NULL),(451280470,4694560,118,NULL,NULL,NULL,NULL),(451280471,4694560,119,NULL,NULL,NULL,NULL),(451280472,4694560,120,NULL,NULL,NULL,NULL),(451280473,4694560,122,NULL,NULL,NULL,NULL),(451280474,4694560,124,NULL,NULL,NULL,NULL),(451280475,4694560,126,NULL,NULL,NULL,NULL),(451280476,4694560,128,NULL,NULL,NULL,NULL),(451280477,4694560,131,NULL,NULL,NULL,NULL),(451280478,4694560,132,NULL,NULL,NULL,NULL),(451280479,4694560,133,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `telegram_update` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tensiobot`
--

DROP TABLE IF EXISTS `tensiobot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tensiobot` (
  `userID` int(11) NOT NULL,
  `clave` varchar(250) NOT NULL,
  `valor` varchar(250) DEFAULT NULL,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`,`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tensiobot`
--

LOCK TABLES `tensiobot` WRITE;
/*!40000 ALTER TABLE `tensiobot` DISABLE KEYS */;
INSERT INTO `tensiobot` VALUES (4694560,'username','juanan3','2017-04-12 22:46:06');
/*!40000 ALTER TABLE `tensiobot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tensiones`
--

DROP TABLE IF EXISTS `tensiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tensiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `clave` varchar(45) NOT NULL,
  `valor` varchar(45) NOT NULL,
  `datecreated` datetime DEFAULT CURRENT_TIMESTAMP,
  `notes` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tensiones`
--

LOCK TABLES `tensiones` WRITE;
/*!40000 ALTER TABLE `tensiones` DISABLE KEYS */;
INSERT INTO `tensiones` VALUES (1,4694560,'tb','90','2017-05-02 10:22:34',NULL),(2,4694560,'ta','140','2017-05-02 10:25:34',NULL),(3,4694560,'ta','145','2017-05-02 10:28:34',NULL),(4,4694560,'tb','96','2017-05-02 10:32:34',NULL),(5,4694560,'tb','95','2017-05-01 10:23:20',NULL),(6,4694560,'ta','145','2017-05-01 10:25:20',NULL),(7,4694560,'ta','146','2017-05-02 10:28:20',NULL),(8,4694560,'tb','100','2017-05-01 10:32:20',NULL),(27,4694560,'lastpic','2017-05-03 23:10:55','2017-05-03 23:10:55',NULL),(28,4694560,'ta','148','2017-05-03 23:07:20',NULL),(29,4694560,'tb','98','2017-05-03 23:07:31',NULL),(30,4694560,'ta','149','2017-05-03 23:07:48',NULL),(31,4694560,'tb','99','2017-05-03 23:07:54',NULL);
/*!40000 ALTER TABLE `tensiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` bigint(20) NOT NULL COMMENT 'Unique user identifier',
  `first_name` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'User''s first name',
  `last_name` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'User''s last name',
  `username` char(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'User''s username',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update',
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (4694560,'Juanan','Pereira','juananpe','2017-05-03 09:50:41','2017-05-03 21:10:50');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_chat`
--

DROP TABLE IF EXISTS `user_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_chat` (
  `user_id` bigint(20) NOT NULL COMMENT 'Unique user identifier',
  `chat_id` bigint(20) NOT NULL COMMENT 'Unique user or chat identifier',
  PRIMARY KEY (`user_id`,`chat_id`),
  KEY `chat_id` (`chat_id`),
  CONSTRAINT `user_chat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_chat_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_chat`
--

LOCK TABLES `user_chat` WRITE;
/*!40000 ALTER TABLE `user_chat` DISABLE KEYS */;
INSERT INTO `user_chat` VALUES (4694560,4694560);
/*!40000 ALTER TABLE `user_chat` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-03 23:15:44
