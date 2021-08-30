CREATE TABLE IF NOT EXISTS `ecommerce_mercadolivre` ( 
`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`appid` text DEFAULT NULL,
`token` text DEFAULT NULL,
`clientsecret` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `ecommerce_mercadolivre` (`id`, `appid`, `clientsecret`) VALUES (1, null, null);
INSERT INTO `ecommerce_plugins` (`id`, `titulo`, `nome`, `tipo`, `path`, `img`, `status`) VALUES (6, 'MercadoLivre', 'mercadolivre', 'mercadolivre', 'ecommerce/plugins/MercadoLivre/MercadoLivre', '', 'checked');
ALTER TABLE `ecommerce` ADD `id_ml` TEXT NULL AFTER `largura`;
ALTER TABLE `ecommerce_estoque` ADD `id_ml` TEXT NULL AFTER `nome`;
ALTER TABLE `ecommerce_estoque` ADD `id_va` VARCHAR(255) NULL AFTER `id_ml`;