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


-- Copiando estrutura do banco de dados para salab4
CREATE DATABASE IF NOT EXISTS `salab4` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `salab4`;

-- Copiando estrutura para tabela salab4.tb_grupos
CREATE TABLE IF NOT EXISTS `tb_grupos` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `grupo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_grupo`),
  KEY `grupo` (`grupo`),
  KEY `id_grupo` (`id_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela salab4.tb_grupos: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_grupos` DISABLE KEYS */;
INSERT INTO `tb_grupos` (`id_grupo`, `grupo`) VALUES
	(1, 'Application\\Controller\\AdministradorController'),
	(2, 'Application\\Controller\\LaboratoristaController'),
	(3, 'Application\\Controller\\ProfessorController');
/*!40000 ALTER TABLE `tb_grupos` ENABLE KEYS */;

-- Copiando estrutura para tabela salab4.tb_laboratorios
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

-- Copiando dados para a tabela salab4.tb_laboratorios: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_laboratorios` DISABLE KEYS */;
INSERT INTO `tb_laboratorios` (`id_laboratorio`, `lab`, `tipo`, `descricao`, `dthr_cad`, `dthr_ult_alteracao`) VALUES
	(2, 'LAB 16', 'ANÁLISES CLÍNICAS', '1° Andar', NULL, NULL);
/*!40000 ALTER TABLE `tb_laboratorios` ENABLE KEYS */;

-- Copiando estrutura para tabela salab4.tb_usuarios
CREATE TABLE IF NOT EXISTS `tb_usuarios` (
  `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT,
  `matricula` bigint(20) DEFAULT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `sobrenome` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` mediumtext,
  `dthr_cad` datetime DEFAULT NULL,
  `dthr_ult_acesso` varchar(50) DEFAULT NULL,
  `dthr_ult_alteracao` datetime DEFAULT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `matricula` (`matricula`),
  KEY `email` (`email`),
  KEY `FK_tb_usuarioss_tb_gruposs` (`id_grupo`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `FK_tb_usuarioss_tb_gruposs` FOREIGN KEY (`id_grupo`) REFERENCES `tb_grupos` (`id_grupo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela salab4.tb_usuarios: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `tb_usuarios` DISABLE KEYS */;
INSERT INTO `tb_usuarios` (`id_usuario`, `matricula`, `nome`, `sobrenome`, `email`, `senha`, `dthr_cad`, `dthr_ult_acesso`, `dthr_ult_alteracao`, `id_grupo`) VALUES
	(3, 20191, 'João', 'Kaio', 'adm@gmail.com', '$2y$10$J5mWj5IkZUewWAfPaY50Ced3khLqu7EWe3SgzTysvEfxbtZA0INdK', '2020-01-25 23:24:39', '2020-01-26 02:24:51', '2020-01-26 00:28:22', 1),
	(8, 20359, 'usu', 'teste', 'asdas@gmail.com', '$2y$10$myCbDCw4yNYAYRvTOfm32Oo/3secGqChSUDd3ugaBPx3gWMvW3s9i', '2020-01-26 01:35:54', NULL, NULL, 2);
/*!40000 ALTER TABLE `tb_usuarios` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
