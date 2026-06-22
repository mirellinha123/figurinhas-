SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8;

create database if not exists jogos;
use jogos;

CREATE TABLE jogos (
  id INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  nome VARCHAR(50) NOT NULL,
  plataforma VARCHAR(50) NOT NULL,
  descricao TEXT NOT NULL,
  dataCad DATETIME NOT NULL,
  foto VARCHAR(50) NOT NULL
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=6;


INSERT INTO `jogos` (`id`, `nome`, `plataforma`, `descricao`, `dataCad`, `foto`) VALUES
(1, 'Pac-Man', 'Arcade (coin-op)', 'Clássico labirinto em que o jogador come pastilhas e foge dos fantasmas.', '2026-06-18 11:00:00', 'pac_man.png'),
(2, 'Space Invaders', 'Arcade (coin-op)', 'Tiro espacial pioneiro em que o jogador defende a Terra de invasores.', '2026-06-18 11:05:00', 'space_invaders.png'),
(3, 'Super Mario Bros.', 'NES / Arcade', 'Plataforma clássico estrelando Mario em sua missão para resgatar a princesa, com power-ups e mundos variados.', '2026-06-18 11:10:00', 'super_mario_bros.png'),
(4, 'Street Fighter II', 'Arcade (coin-op)', 'Jogo de luta que definiu o gênero competitivo com personagens icônicos e combos.', '2026-06-18 11:15:00', 'street_fighter_ii.png'),
(5, 'Galaga', 'Arcade (coin-op)', 'Jogo de tiro espacial com formações inimigas e fases de bônus.', '2026-06-18 11:20:00', 'galaga.png'),
(6, 'Mortal Kombat', 'Arcade (coin-op)', 'Jogo de luta famoso por golpes, fatalities e gráficos polêmicos na época.', '2026-06-18 11:25:00', 'mortal_kombat.png');
