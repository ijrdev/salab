-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           5.7.28-0ubuntu0.18.04.4 - (Ubuntu)
-- OS do Servidor:               Linux
-- HeidiSQL Versão:              10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para salab3
CREATE DATABASE IF NOT EXISTS `salab3` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `salab3`;

-- Copiando estrutura para tabela salab3.tb_grupos
CREATE TABLE IF NOT EXISTS `tb_grupos` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `grupo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_grupo`),
  KEY `grupo` (`grupo`),
  KEY `id_grupo` (`id_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela salab3.tb_grupos: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_grupos` DISABLE KEYS */;
INSERT INTO `tb_grupos` (`id_grupo`, `grupo`) VALUES
	(1, 'Application\\Controller\\AdministradorController'),
	(2, 'Application\\Controller\\LaboratoristaController'),
	(3, 'Application\\Controller\\ProfessorController');
/*!40000 ALTER TABLE `tb_grupos` ENABLE KEYS */;

-- Copiando estrutura para tabela salab3.tb_laboratorios
CREATE TABLE IF NOT EXISTS `tb_laboratorios` (
  `id_laboratorio` int(11) NOT NULL AUTO_INCREMENT,
  `lab` varchar(100) DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `descricao` varchar(100) DEFAULT NULL,
  `dthr_cad` datetime DEFAULT NULL,
  `dthr_ult_alteracao` datetime DEFAULT NULL,
  PRIMARY KEY (`id_laboratorio`),
  KEY `id_laboratorio` (`id_laboratorio`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela salab3.tb_laboratorios: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_laboratorios` DISABLE KEYS */;
INSERT INTO `tb_laboratorios` (`id_laboratorio`, `lab`, `tipo`, `descricao`, `dthr_cad`, `dthr_ult_alteracao`) VALUES
	(1, 'LAB 11', 'ANÁLISES E MEDIDAS (LAMP)', '1° Andar (Lob/Lamp)', NULL, NULL),
	(2, 'LAB 16', 'ANÁLISES CLÍNICAS', '1° Andar', NULL, NULL),
	(3, 'LAB 05', 'ANATOMIA SINTÉTICA', 'Térreo (Anatomia 3)', NULL, NULL);
/*!40000 ALTER TABLE `tb_laboratorios` ENABLE KEYS */;

-- Copiando estrutura para tabela salab3.tb_usuarios
CREATE TABLE IF NOT EXISTS `tb_usuarios` (
  `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT,
  `matricula` bigint(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` mediumtext,
  `dthr_cad` datetime DEFAULT NULL,
  `dthr_ult_alteracao` datetime DEFAULT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `matricula` (`matricula`),
  KEY `email` (`email`),
  KEY `FK_tb_usuarioss_tb_gruposs` (`id_grupo`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `FK_tb_usuarioss_tb_gruposs` FOREIGN KEY (`id_grupo`) REFERENCES `tb_grupos` (`id_grupo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela salab3.tb_usuarios: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_usuarios` DISABLE KEYS */;
INSERT INTO `tb_usuarios` (`id_usuario`, `matricula`, `email`, `senha`, `dthr_cad`, `dthr_ult_alteracao`, `id_grupo`) VALUES
	(3, 20191, 'adm@gmail.com', '$2y$10$xKhneSRWzwOB1.pxV4t.yura1C.De2X/XMAAnTz2lvgHXj.ko.12a', NULL, NULL, 1),
	(5, 20195, 'a@gmail.com', '$2y$10$4AkZ66Ut6GM3TCwdSJytheGj17tWgtA5PrVT0ncaz2bdvPBl3N76W', NULL, '2020-01-25 07:40:34', 3),
	(7, 20192, 'labor@gmail.com', '$2y$10$GnuRhttt/QQToY9WEn6yI.ybp38v8b9V6gm554NNjVKvzs4jPbekO', '2020-01-25 03:57:22', NULL, 2);
/*!40000 ALTER TABLE `tb_usuarios` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
