-- --------------------------------------------------------
-- Servidor:                     192.168.66.116
-- Versão do servidor:           5.7.29-0ubuntu0.18.04.1 - (Ubuntu)
-- OS do Servidor:               Linux
-- HeidiSQL Versão:              10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para testes_sql
CREATE DATABASE IF NOT EXISTS `testes_sql` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `testes_sql`;

-- Copiando estrutura para tabela testes_sql.nacionalidades_tb
CREATE TABLE IF NOT EXISTS `nacionalidades_tb` (
  `id_nacionalidade` int(11) NOT NULL AUTO_INCREMENT,
  `nacionalidade` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_nacionalidade`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela testes_sql.nacionalidades_tb: ~7 rows (aproximadamente)
/*!40000 ALTER TABLE `nacionalidades_tb` DISABLE KEYS */;
INSERT INTO `nacionalidades_tb` (`id_nacionalidade`, `nacionalidade`) VALUES
	(1, 'ANGOLA'),
	(2, 'BRASIL'),
	(3, 'BULGÁRIA'),
	(4, 'CANADÁ'),
	(5, 'CATAR'),
	(6, 'EMIRADOS ÁRABES'),
	(7, 'EQUADOR');
/*!40000 ALTER TABLE `nacionalidades_tb` ENABLE KEYS */;

-- Copiando estrutura para tabela testes_sql.profissoes_tb
CREATE TABLE IF NOT EXISTS `profissoes_tb` (
  `id_profissao` int(11) NOT NULL AUTO_INCREMENT,
  `profissao` varchar(50) DEFAULT NULL,
  `salario` float DEFAULT NULL,
  PRIMARY KEY (`id_profissao`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela testes_sql.profissoes_tb: ~7 rows (aproximadamente)
/*!40000 ALTER TABLE `profissoes_tb` DISABLE KEYS */;
INSERT INTO `profissoes_tb` (`id_profissao`, `profissao`, `salario`) VALUES
	(1, 'Investigador particular', 2000),
	(2, 'Guitarrista', 1200),
	(3, 'Guia turistico', 950),
	(4, 'Juiz de direito', 8000),
	(5, 'Maquiador', 1050),
	(6, 'Operador de telemarketing', 800),
	(7, 'Oftalmologista', 4000);
/*!40000 ALTER TABLE `profissoes_tb` ENABLE KEYS */;

-- Copiando estrutura para tabela testes_sql.usuarios_tb
CREATE TABLE IF NOT EXISTS `usuarios_tb` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  `id_nacionalidade` int(11) DEFAULT NULL,
  `id_profissao` int(11) DEFAULT NULL,
  `dt_cadastro` date DEFAULT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela testes_sql.usuarios_tb: ~13 rows (aproximadamente)
/*!40000 ALTER TABLE `usuarios_tb` DISABLE KEYS */;
INSERT INTO `usuarios_tb` (`id_usuario`, `nome`, `id_nacionalidade`, `id_profissao`, `dt_cadastro`) VALUES
	(1, 'Caio', 3, 7, '2013-12-12'),
	(2, 'Cauan', 7, 4, '2019-01-17'),
	(3, 'Higor', 3, 1, '2020-03-01'),
	(4, 'Nathan', 3, 4, '2020-10-15'),
	(5, 'Suzan', 5, 5, '2019-03-03'),
	(6, 'Mirelly', 2, 2, '2019-01-05'),
	(7, 'Anna', 3, 7, '2013-04-04'),
	(8, 'Arian', 7, 6, '2018-06-25'),
	(9, 'César', 6, 4, '2015-02-28'),
	(10, 'Larysa', 6, 4, '2017-05-22'),
	(11, 'Cristian', 6, 2, '2015-09-16'),
	(12, 'Junior', 2, 3, '2017-09-10'),
	(13, 'Rennan', 7, 1, '2020-02-12'),
	(14, 'Paulo', 1, 3, NULL);
/*!40000 ALTER TABLE `usuarios_tb` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
