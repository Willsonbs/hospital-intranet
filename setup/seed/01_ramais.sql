-- Dados fictícios do diretório de ramais (Hospital Santa Aurora)
INSERT INTO joom_hospital_ramais (setor, ramal, localizacao, state, ordering, created, modified) VALUES
('Recepção', '2010', 'Térreo', 1, 1, NOW(), NOW()),
('Tecnologia da Informação', '2045', '1º andar', 1, 2, NOW(), NOW()),
('Enfermagem', '2080', '2º andar', 1, 3, NOW(), NOW()),
('Recursos Humanos', '2090', 'Prédio Administrativo', 1, 4, NOW(), NOW()),
('Financeiro', '2100', 'Prédio Administrativo', 1, 5, NOW(), NOW()),
('Farmácia', '2120', 'Térreo', 1, 6, NOW(), NOW()),
('Faturamento', '2105', 'Prédio Administrativo', 1, 7, NOW(), NOW()),
('Centro Cirúrgico', '2200', 'Centro Cirúrgico', 1, 8, NOW(), NOW()),
('UTI Adulto', '2250', 'UTI', 1, 9, NOW(), NOW()),
('Almoxarifado', '2130', 'Subsolo', 1, 10, NOW(), NOW()),
('Ambulatório', '2060', 'Ambulatório', 1, 11, NOW(), NOW()),
('Suporte de TI', '2000', '1º andar', 1, 12, NOW(), NOW()),
('Nutrição e Dietética', '2140', 'Subsolo', 1, 13, NOW(), NOW()),
('Qualidade e Educação Permanente', '2095', 'Prédio Administrativo', 1, 14, NOW(), NOW()),
('Imprensa e Comunicação', '2098', 'Prédio Administrativo', 1, 15, NOW(), NOW()),
('Pronto-Socorro', '2020', 'Térreo', 1, 16, NOW(), NOW()),
('Setor desativado (exemplo)', '2999', 'Térreo', 0, 99, NOW(), NOW());

-- Item "Ramais" no menu principal (filho da raiz do nested set)
SET @cid := (SELECT extension_id FROM joom_extensions WHERE element = 'com_ramais' AND type = 'component');
SET @r := (SELECT rgt FROM joom_menu WHERE id = 1);
INSERT INTO joom_menu (menutype, title, alias, note, path, link, type, published, parent_id, level, component_id,
  checked_out, checked_out_time, browserNav, access, img, template_style_id, params, lft, rgt, home, language, client_id, publish_up, publish_down)
VALUES ('mainmenu', 'Ramais', 'ramais', '', 'ramais', 'index.php?option=com_ramais&view=ramais', 'component', 1, 1, 1, @cid,
  NULL, NULL, 0, 1, ' ', 0, '{"default_ordering":"setor","intro":"Consulte rapidamente o ramal e a localização dos setores."}', @r, @r + 1, 0, '*', 0, NULL, NULL);
UPDATE joom_menu SET rgt = @r + 2 WHERE id = 1;
