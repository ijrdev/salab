-- --------------------------------------------------------
-- Servidor:                     157.245.123.80
-- Versão do servidor:           5.7.29-0ubuntu0.18.04.1 - (Ubuntu)
-- OS do Servidor:               Linux
-- HeidiSQL Versão:              10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para salab
CREATE DATABASE IF NOT EXISTS `salab` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `salab`;

-- Copiando estrutura para tabela salab.tb_agendamentos
CREATE TABLE IF NOT EXISTS `tb_agendamentos` (
  `id_agendamento` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_reserva` bigint(20) DEFAULT NULL,
  `id_usuario` bigint(20) DEFAULT NULL,
  `disciplina` varchar(50) DEFAULT NULL,
  `horario` varchar(50) DEFAULT NULL,
  `observacao` varchar(100) DEFAULT NULL,
  `status` char(1) DEFAULT 'D' COMMENT 'D - Disponível; I - Indisponível;',
  `dt_agendamento` date DEFAULT NULL,
  PRIMARY KEY (`id_agendamento`),
  KEY `FK_tb_agendamentos_tb_reservas` (`id_reserva`),
  KEY `FK_tb_agendamentos_tb_usuarios` (`id_usuario`),
  KEY `horario` (`horario`),
  CONSTRAINT `FK_tb_agendamentos_tb_reservas` FOREIGN KEY (`id_reserva`) REFERENCES `tb_reservas` (`id_reserva`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_tb_agendamentos_tb_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_agendamentos: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_agendamentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_agendamentos` ENABLE KEYS */;

-- Copiando estrutura para tabela salab.tb_anexos
CREATE TABLE IF NOT EXISTS `tb_anexos` (
  `id_anexo` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) DEFAULT NULL,
  `segmento` char(3) DEFAULT NULL COMMENT 'ANX = Anexo; PRF = Perfil;',
  `dthr_cad` datetime DEFAULT NULL,
  `nome_anexo` varchar(100) DEFAULT NULL,
  `extensao` varchar(100) DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `tamanho` int(11) DEFAULT NULL,
  `arquivo` mediumblob,
  PRIMARY KEY (`id_anexo`),
  KEY `FK_tb_anexos_tb_usuarios` (`id_usuario`),
  CONSTRAINT `FK_tb_anexos_tb_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_anexos: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_anexos` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_anexos` ENABLE KEYS */;

-- Copiando estrutura para tabela salab.tb_avisos
CREATE TABLE IF NOT EXISTS `tb_avisos` (
  `id_aviso` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) DEFAULT NULL,
  `mensagem` varchar(350) DEFAULT NULL,
  `dthr_aviso` datetime DEFAULT NULL,
  PRIMARY KEY (`id_aviso`),
  KEY `FK_tb_avisos_tb_usuarios` (`id_usuario`),
  CONSTRAINT `FK_tb_avisos_tb_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_avisos: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_avisos` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_avisos` ENABLE KEYS */;

-- Copiando estrutura para tabela salab.tb_avisos_anexos
CREATE TABLE IF NOT EXISTS `tb_avisos_anexos` (
  `id_anexo_aviso` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_aviso` bigint(20) DEFAULT NULL,
  `id_anexo` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_anexo_aviso`),
  KEY `FK_tb_avisos_anexos_tb_avisos` (`id_aviso`),
  KEY `FK_tb_avisos_anexos_tb_anexos` (`id_anexo`),
  CONSTRAINT `FK_tb_avisos_anexos_tb_anexos` FOREIGN KEY (`id_anexo`) REFERENCES `tb_anexos` (`id_anexo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_tb_avisos_anexos_tb_avisos` FOREIGN KEY (`id_aviso`) REFERENCES `tb_avisos` (`id_aviso`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_avisos_anexos: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_avisos_anexos` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_avisos_anexos` ENABLE KEYS */;

-- Copiando estrutura para tabela salab.tb_grupos
CREATE TABLE IF NOT EXISTS `tb_grupos` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `grupo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_grupo`),
  KEY `grupo` (`grupo`),
  KEY `id_grupo` (`id_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_grupos: ~2 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_grupos` DISABLE KEYS */;
INSERT INTO `tb_grupos` (`id_grupo`, `grupo`) VALUES
	(1, 'Application\\Controller\\AdministradorController'),
	(2, 'Application\\Controller\\LaboratoristaController'),
	(3, 'Application\\Controller\\ProfessorController');
/*!40000 ALTER TABLE `tb_grupos` ENABLE KEYS */;

-- Copiando estrutura para tabela salab.tb_laboratorios
CREATE TABLE IF NOT EXISTS `tb_laboratorios` (
  `id_laboratorio` bigint(20) NOT NULL AUTO_INCREMENT,
  `lab` varchar(100) DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `descricao` varchar(100) DEFAULT NULL,
  `dthr_cad` datetime DEFAULT NULL,
  `situacao` char(1) DEFAULT 'A' COMMENT 'A -ATIVO; I - INATIVO;',
  PRIMARY KEY (`id_laboratorio`),
  KEY `id_laboratorio` (`id_laboratorio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_laboratorios: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_laboratorios` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_laboratorios` ENABLE KEYS */;

-- Copiando estrutura para tabela salab.tb_logs
CREATE TABLE IF NOT EXISTS `tb_logs` (
  `id_log` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) DEFAULT NULL,
  `dthr_log` datetime DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `sql` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_logs: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_logs` ENABLE KEYS */;

-- Copiando estrutura para tabela salab.tb_reservas
CREATE TABLE IF NOT EXISTS `tb_reservas` (
  `id_reserva` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_laboratorio` bigint(20) DEFAULT NULL,
  `dt_reserva` date DEFAULT NULL,
  `manha` char(1) DEFAULT '0',
  `tarde` char(1) DEFAULT '0',
  `noite` char(1) DEFAULT '0',
  PRIMARY KEY (`id_reserva`),
  KEY `FK_tb_reservas_tb_laboratorios` (`id_laboratorio`),
  CONSTRAINT `FK_tb_reservas_tb_laboratorios` FOREIGN KEY (`id_laboratorio`) REFERENCES `tb_laboratorios` (`id_laboratorio`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_reservas: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_reservas` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_reservas` ENABLE KEYS */;

-- Copiando estrutura para tabela salab.tb_usuarios
CREATE TABLE IF NOT EXISTS `tb_usuarios` (
  `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT,
  `matricula` bigint(20) DEFAULT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `sobrenome` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` mediumtext,
  `dthr_cad` datetime DEFAULT NULL,
  `dthr_ult_alteracao` datetime DEFAULT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `situacao` char(1) DEFAULT 'A' COMMENT 'A - Ativo; I - Inativo',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `matricula` (`matricula`),
  UNIQUE KEY `email` (`email`),
  KEY `FK_tb_usuarioss_tb_gruposs` (`id_grupo`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `FK_tb_usuarioss_tb_gruposs` FOREIGN KEY (`id_grupo`) REFERENCES `tb_grupos` (`id_grupo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela salab.tb_usuarios: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_usuarios` DISABLE KEYS */;
INSERT INTO `tb_usuarios` (`id_usuario`, `matricula`, `nome`, `sobrenome`, `email`, `senha`, `dthr_cad`, `dthr_ult_alteracao`, `id_grupo`, `situacao`) VALUES
	(1, 2020, 'Ivanildo', 'Junior', 'ivanildo.m.g.junior@gmail.com', '$2y$10$WXi48Ibq2l1COXIdWVaBiuAOjyVLJkBDE.BmECeGs2g3lPI0d6zo2', '2020-02-22 15:43:12', NULL, 1, 'A');
/*!40000 ALTER TABLE `tb_usuarios` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
