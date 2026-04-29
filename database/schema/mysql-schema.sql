/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;
DROP TABLE IF EXISTS `alias_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alias_groups` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `color` varchar(32) DEFAULT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_groups_user_id_name_unique` (`user_id`,`name`),
  KEY `alias_groups_user_id_sort_order_index` (`user_id`,`sort_order`),
  CONSTRAINT `alias_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alias_leak_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alias_leak_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alias_id` char(36) NOT NULL,
  `sender_domain` varchar(255) NOT NULL,
  `detected_at` timestamp NOT NULL,
  `status` enum('pending','confirmed','dismissed') NOT NULL DEFAULT 'pending',
  `notified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_leak_events_alias_id_sender_domain_unique` (`alias_id`,`sender_domain`),
  KEY `alias_leak_events_alias_id_status_index` (`alias_id`,`status`),
  CONSTRAINT `alias_leak_events_alias_id_foreign` FOREIGN KEY (`alias_id`) REFERENCES `aliases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alias_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alias_recipients` (
  `id` char(36) NOT NULL,
  `alias_id` char(36) NOT NULL,
  `recipient_id` char(36) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_recipients_alias_id_recipient_id_unique` (`alias_id`,`recipient_id`),
  KEY `alias_recipients_recipient_id_index` (`recipient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alias_sender_observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alias_sender_observations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alias_id` char(36) NOT NULL,
  `sender_domain` varchar(255) NOT NULL,
  `email_count` int(10) unsigned NOT NULL DEFAULT 1,
  `first_seen_at` timestamp NOT NULL,
  `last_seen_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_sender_observations_alias_id_sender_domain_unique` (`alias_id`,`sender_domain`),
  KEY `alias_sender_observations_alias_id_index` (`alias_id`),
  CONSTRAINT `alias_sender_observations_alias_id_foreign` FOREIGN KEY (`alias_id`) REFERENCES `aliases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `aliases` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `aliasable_id` char(36) DEFAULT NULL,
  `aliasable_type` varchar(255) DEFAULT NULL,
  `alias_group_id` char(36) DEFAULT NULL,
  `local_part` varchar(255) NOT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `domain` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `pinned` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `from_name` text DEFAULT NULL,
  `attached_recipients_only` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `max_emails` int(10) unsigned DEFAULT NULL,
  `on_expiry` enum('discard','bounce') NOT NULL DEFAULT 'discard',
  `expired_at` timestamp NULL DEFAULT NULL,
  `baseline_sender_domain` varchar(255) DEFAULT NULL,
  `baseline_locked_at` timestamp NULL DEFAULT NULL,
  `ghost_mode` tinyint(1) NOT NULL DEFAULT 0,
  `emails_forwarded` int(10) unsigned NOT NULL DEFAULT 0,
  `emails_blocked` int(10) unsigned NOT NULL DEFAULT 0,
  `emails_replied` int(10) unsigned NOT NULL DEFAULT 0,
  `emails_sent` int(10) unsigned NOT NULL DEFAULT 0,
  `last_forwarded` timestamp NULL DEFAULT NULL,
  `last_blocked` timestamp NULL DEFAULT NULL,
  `last_replied` timestamp NULL DEFAULT NULL,
  `last_sent` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aliases_email_unique` (`email`),
  KEY `aliases_user_id_index` (`user_id`),
  KEY `aliases_aliasable_id_index` (`aliasable_id`),
  KEY `aliases_expires_active_idx` (`expires_at`,`active`),
  KEY `aliases_ghost_mode_index` (`ghost_mode`),
  KEY `aliases_alias_group_id_index` (`alias_group_id`),
  CONSTRAINT `aliases_alias_group_id_foreign` FOREIGN KEY (`alias_group_id`) REFERENCES `alias_groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blocked_senders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blocked_senders` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `type` varchar(10) NOT NULL,
  `value` varchar(255) NOT NULL,
  `blocked` int(10) unsigned NOT NULL DEFAULT 0,
  `last_blocked` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blocked_senders_user_id_type_value_unique` (`user_id`,`type`,`value`),
  KEY `blocked_senders_value_index` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `deleted_usernames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deleted_usernames` (
  `username` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `domains` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `default_recipient_id` char(36) DEFAULT NULL,
  `domain` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `from_name` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `catch_all` tinyint(1) NOT NULL DEFAULT 1,
  `auto_create_regex` varchar(255) DEFAULT NULL,
  `domain_verified_at` timestamp NULL DEFAULT NULL,
  `domain_mx_validated_at` timestamp NULL DEFAULT NULL,
  `domain_sending_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domains_domain_unique` (`domain`),
  KEY `domains_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_deliveries` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `recipient_id` char(36) DEFAULT NULL,
  `alias_id` char(36) DEFAULT NULL,
  `is_stored` tinyint(1) NOT NULL DEFAULT 0,
  `resent` tinyint(1) NOT NULL DEFAULT 0,
  `bounce_type` varchar(4) DEFAULT NULL,
  `remote_mta` varchar(255) DEFAULT NULL,
  `sender` text DEFAULT NULL,
  `destination` text DEFAULT NULL,
  `ir_dedupe_key` varchar(64) DEFAULT NULL,
  `email_type` varchar(5) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `code` text DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_deliveries_ir_dedupe_key_unique` (`ir_dedupe_key`),
  KEY `failed_deliveries_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `outbound_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `outbound_messages` (
  `id` varchar(12) NOT NULL,
  `user_id` char(36) NOT NULL,
  `alias_id` char(36) DEFAULT NULL,
  `recipient_id` char(36) DEFAULT NULL,
  `email_type` varchar(5) NOT NULL,
  `encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `bounced` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outbound_messages_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` char(36) NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipients` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `email` text NOT NULL,
  `can_reply_send` tinyint(1) NOT NULL DEFAULT 1,
  `should_encrypt` tinyint(1) NOT NULL DEFAULT 0,
  `inline_encryption` tinyint(1) NOT NULL DEFAULT 0,
  `protected_headers` tinyint(1) NOT NULL DEFAULT 0,
  `remove_pgp_keys` tinyint(1) NOT NULL DEFAULT 1,
  `remove_pgp_signatures` tinyint(1) NOT NULL DEFAULT 1,
  `fingerprint` text DEFAULT NULL,
  `pending` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipients_user_id_index` (`user_id`),
  KEY `recipients_user_pending_created_at_index` (`user_id`,`pending`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `redirect_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirect_tokens` (
  `token` varchar(16) NOT NULL,
  `alias_id` char(36) DEFAULT NULL,
  `target_url` text NOT NULL,
  `clicks` int(10) unsigned NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `redirect_tokens_expires_at_index` (`expires_at`),
  KEY `redirect_tokens_alias_id_foreign` (`alias_id`),
  CONSTRAINT `redirect_tokens_alias_id_foreign` FOREIGN KEY (`alias_id`) REFERENCES `aliases` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rules` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `order` int(10) unsigned NOT NULL DEFAULT 0,
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`conditions`)),
  `actions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`actions`)),
  `operator` varchar(3) NOT NULL DEFAULT 'AND',
  `forwards` tinyint(1) NOT NULL DEFAULT 0,
  `replies` tinyint(1) NOT NULL DEFAULT 0,
  `sends` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `applied` int(10) unsigned NOT NULL DEFAULT 0,
  `last_applied` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rules_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stored_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stored_emails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alias_id` char(36) NOT NULL,
  `from_preview` varchar(128) DEFAULT NULL,
  `subject_preview` varchar(128) DEFAULT NULL,
  `size_bytes` int(10) unsigned NOT NULL DEFAULT 0,
  `encrypted_payload` longtext NOT NULL,
  `received_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stored_emails_alias_id_received_at_index` (`alias_id`,`received_at`),
  CONSTRAINT `stored_emails_alias_id_foreign` FOREIGN KEY (`alias_id`) REFERENCES `aliases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscription_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `stripe_id` varchar(255) NOT NULL,
  `stripe_product` varchar(255) NOT NULL,
  `stripe_price` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_items_stripe_id_unique` (`stripe_id`),
  KEY `subscription_items_subscription_id_stripe_price_index` (`subscription_id`,`stripe_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `stripe_id` varchar(255) NOT NULL,
  `stripe_status` varchar(255) NOT NULL,
  `stripe_price` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriptions_stripe_id_unique` (`stripe_id`),
  KEY `subscriptions_user_id_stripe_status_index` (`user_id`,`stripe_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `usernames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usernames` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `default_recipient_id` char(36) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `from_name` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `catch_all` tinyint(1) NOT NULL DEFAULT 1,
  `auto_create_regex` varchar(255) DEFAULT NULL,
  `can_login` tinyint(1) NOT NULL DEFAULT 1,
  `external_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usernames_username_unique` (`username`),
  UNIQUE KEY `usernames_external_id_unique` (`external_id`),
  KEY `usernames_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `plan` varchar(255) NOT NULL DEFAULT 'free',
  `plan_expires_at` timestamp NULL DEFAULT NULL,
  `stripe_id` varchar(255) DEFAULT NULL,
  `stripe_subscription_id` varchar(255) DEFAULT NULL,
  `pm_type` varchar(255) DEFAULT NULL,
  `pm_last_four` varchar(4) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `default_username_id` char(36) NOT NULL,
  `from_name` text DEFAULT NULL,
  `email_subject` text DEFAULT NULL,
  `strip_trackers` enum('off','pixels_only','pixels_and_links') NOT NULL DEFAULT 'off',
  `banner_location` varchar(255) NOT NULL DEFAULT 'top',
  `spam_warning_behaviour` varchar(255) NOT NULL DEFAULT 'banner',
  `list_unsubscribe_behaviour` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `display_from_format` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `login_redirect` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `bandwidth` int(10) unsigned NOT NULL DEFAULT 0,
  `reject_until` timestamp NULL DEFAULT NULL,
  `defer_until` timestamp NULL DEFAULT NULL,
  `defer_new_aliases_until` timestamp NULL DEFAULT NULL,
  `username_count` int(10) unsigned NOT NULL DEFAULT 0,
  `default_recipient_id` char(36) NOT NULL,
  `default_alias_domain` varchar(255) DEFAULT NULL,
  `default_alias_format` varchar(255) DEFAULT NULL,
  `alias_separator` varchar(10) NOT NULL DEFAULT '.',
  `use_reply_to` tinyint(1) NOT NULL DEFAULT 0,
  `store_failed_deliveries` tinyint(1) NOT NULL DEFAULT 1,
  `save_alias_last_used` tinyint(1) NOT NULL DEFAULT 1,
  `dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `webauthn_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_backup_code` varchar(100) DEFAULT NULL,
  `vault_public_key` longtext DEFAULT NULL,
  `vault_encrypted_private_key` longtext DEFAULT NULL,
  `vault_created_at` timestamp NULL DEFAULT NULL,
  `ghost_lock_minutes` smallint(5) unsigned NOT NULL DEFAULT 15,
  `ghost_preview_mode` enum('preview_10','encrypted') NOT NULL DEFAULT 'preview_10',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_google_id_unique` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `webauthn_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `webauthn_keys` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT 'key',
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `credentialId` mediumtext NOT NULL,
  `type` varchar(255) NOT NULL,
  `transports` text NOT NULL,
  `attestationType` varchar(255) NOT NULL,
  `trustPath` text NOT NULL,
  `aaguid` text NOT NULL,
  `credentialPublicKey` text NOT NULL,
  `counter` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webauthn_keys_user_id_foreign` (`user_id`),
  CONSTRAINT `webauthn_keys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `webhook_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhook_deliveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `webhook_id` char(36) NOT NULL,
  `event` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `status` enum('pending','success','failed','giving_up') NOT NULL DEFAULT 'pending',
  `response_code` smallint(5) unsigned DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `next_retry_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webhook_deliveries_webhook_id_index` (`webhook_id`),
  KEY `webhook_deliveries_status_next_retry_at_index` (`status`,`next_retry_at`),
  CONSTRAINT `webhook_deliveries_webhook_id_foreign` FOREIGN KEY (`webhook_id`) REFERENCES `webhooks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhooks` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `url` varchar(255) NOT NULL,
  `events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`events`)),
  `secret` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `last_delivered_at` timestamp NULL DEFAULT NULL,
  `last_response_code` smallint(5) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webhooks_user_id_index` (`user_id`),
  CONSTRAINT `webhooks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

/*M!999999\- enable the sandbox mode */ 
SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2016_06_01_000001_create_oauth_auth_codes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2016_06_01_000002_create_oauth_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2016_06_01_000003_create_oauth_refresh_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2016_06_01_000004_create_oauth_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2016_06_01_000005_create_oauth_personal_access_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2019_02_14_161733_create_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2019_02_18_132037_create_recipients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2019_02_19_110850_create_alias_recipients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2019_05_15_170256_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2019_05_16_090909_create_deleted_usernames_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2019_05_22_171424_add_local_part_and_domain_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2019_05_22_180706_remove_nullable_from_local_part_and_domain_on_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2019_05_31_092710_add_emails_replied_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2019_05_31_104343_create_domains_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2019_05_31_140544_add_domain_id_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2019_06_03_154556_add_from_name_and_banner_location_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2019_06_04_121334_add_two_factor_auth_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2019_06_06_113031_add_bandwidth_column_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2019_07_11_133147_add_openpgp_fields_to_recipients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2019_07_17_101326_add_email_subject_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2019_08_01_090418_add_extension_column_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2019_08_05_093129_create_additional_usernames_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2019_08_05_111548_add_username_count_column_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2019_08_21_104530_add_domain_verified_at_to_domains_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2019_09_12_141803_add_two_factor_backup_code_column_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2019_09_24_153605_add_default_recipient_id_column_to_domains_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2019_11_05_100750_add_default_recipient_id_column_to_additional_usernames_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2019_11_05_105047_add_aliasable_columns_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2019_11_05_143834_remove_domain_id_column_from_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2019_12_09_151843_add_domain_sending_verified_at_to_domains_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2020_01_21_132726_add_emails_sent_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2020_02_28_111536_add_default_alias_domain_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2020_03_05_112308_create_rules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2020_06_02_074735_add_provider_to_oauth_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2020_06_18_105206_add_default_alias_format_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2020_09_26_084912_update_aliasable_type_in_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2020_10_07_141852_add_catch_all_to_domains_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2020_10_09_115344_add_catch_all_to_additional_usernames_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2020_10_13_091421_add_catch_all_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2020_11_24_120152_create_webauthn_keys_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2021_02_24_121035_add_use_reply_to_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2021_07_05_141300_create_postfix_queue_ids_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2021_07_14_140246_add_domain_mx_validated_at_to_domains_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2021_07_15_083825_create_failed_deliveries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2021_08_03_085607_add_attempted_at_to_failed_deliveries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2021_08_04_104448_add_enabled_to_webauthn_keys_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2021_12_03_155539_add_email_types_to_rules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2022_02_09_150610_update_credential_id_value_in_webauthn_keys_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2022_02_21_123001_add_can_reply_send_to_recipients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2022_02_25_091005_move_account_username_to_usernames_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2022_03_14_111720_update_code_in_failed_deliveries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2022_06_29_103709_update_email_type_in_failed_deliveries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2022_07_19_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2022_07_29_111323_add_expires_at_to_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2022_08_05_085825_add_protected_headers_and_inline_encryption_to_recipients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2022_11_11_113130_add_defer_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2022_11_21_164512_add_reject_until_column_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2022_11_24_135311_create_outbound_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2023_01_20_111533_add_destination_to_failed_deliveries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2023_01_31_110006_drop_postfix_queue_ids_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2023_03_22_101343_add_indexes_to_user_id_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2023_03_24_151053_add_index_for_aliasable_id_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2023_04_28_145204_add_columns_for_storing_failed_deliveries',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2023_05_31_131832_add_from_name_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2023_06_05_111322_add_pending_column_to_recipients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2023_06_29_155657_add_can_login_to_usernames_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2023_06_30_101305_add_display_from_format_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2023_11_02_155035_add_login_redirect_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2024_03_19_114553_add_last_used_columns_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2024_03_20_155004_add_save_alias_last_used_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2024_05_02_142415_add_applied_columns_to_rules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2024_07_03_110530_add_auto_create_regex_to_usernames_and_domains_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2024_11_18_104417_add_external_id_to_username',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2025_04_08_123940_add_attached_recipients_only_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2025_06_14_071225_add_resent_to_failed_deliveries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2025_07_17_090821_add_encrypted_column_to_outbound_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2025_09_30_130929_add_separate_2fa_flags_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2025_12_10_104145_change_credential_id_length_in_webauthn_keys_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2025_12_23_144749_add_dark_mode_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2026_01_08_113654_add_new_columns_to_recipients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_02_04_110618_add_spam_warning_behaviour_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_02_04_155950_add_alias_separator_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_02_05_120000_add_list_unsubscribe_behaviour_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_02_18_155002_create_blocked_senders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2026_04_07_121451_add_blocked_columns_to_blocked_senders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2026_04_08_102920_add_pinned_to_aliases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_04_09_153657_add_ir_dedupe_key_to_failed_deliveries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_04_09_163347_add_performance_indexes_for_recipients_and_alias_recipients',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_04_15_100000_add_plan_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_04_15_140000_add_google_id_to_users_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2026_04_17_100000_add_cashier_columns_to_users_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2026_04_17_120000_rename_stripe_customer_id_to_stripe_id',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2026_04_20_110141_add_burner_columns_to_aliases_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2026_04_20_111606_create_alias_sender_observations_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2026_04_20_112122_create_redirect_tokens_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2026_04_20_112244_add_strip_trackers_to_users_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2026_04_20_123705_create_webhooks_tables',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2026_04_20_125654_create_ghost_inbox_tables',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2026_04_21_081527_create_alias_groups_table',11);
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
