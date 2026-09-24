CREATE TABLE IF NOT EXISTS `#__ramais` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `setor` varchar(150) NOT NULL DEFAULT '',
  `ramal` varchar(30) NOT NULL DEFAULT '',
  `localizacao` varchar(150) NOT NULL DEFAULT '',
  `state` tinyint NOT NULL DEFAULT 1 COMMENT '1 = ativo, 0 = inativo, -2 = lixeira',
  `ordering` int NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned,
  `checked_out_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_setor` (`setor`),
  KEY `idx_checkout` (`checked_out`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
