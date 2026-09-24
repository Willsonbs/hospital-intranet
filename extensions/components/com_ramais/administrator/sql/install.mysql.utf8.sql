CREATE TABLE IF NOT EXISTS `#__hospital_ramais` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `setor` varchar(150) NOT NULL DEFAULT '',
  `ramal` varchar(20) NOT NULL DEFAULT '',
  `localizacao` varchar(150) NOT NULL DEFAULT '',
  `state` tinyint NOT NULL DEFAULT 1,
  `ordering` int NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned,
  `checked_out_time` datetime,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_setor` (`setor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
