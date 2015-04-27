-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Machine: 127.0.0.1
-- Gegenereerd op: 27 apr 2015 om 20:37
-- Serverversie: 5.6.21
-- PHP-versie: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `dungeons_and_dragons`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `advantages`
--

CREATE TABLE IF NOT EXISTS `advantages` (
  `advantage_id` int(11) NOT NULL,
  `condition_id` int(11) NOT NULL,
  `basic_id` int(11) NOT NULL,
  `advantage_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `advantages`
--

INSERT INTO `advantages` (`advantage_id`, `condition_id`, `basic_id`, `advantage_value`) VALUES
(0, 0, 0, '0'),
(1, 1, 1, '2'),
(2, 1, 0, '-2'),
(3, 1, 9, '-2'),
(4, 2, 9, '-5'),
(5, 3, 2, '-1'),
(6, 4, 10, 'Alle vijanden zullen jou aanvallen ipv de anderen. Laat de DM dit weten.'),
(7, 5, 0, '-5'),
(8, 5, 1, '-5'),
(9, 6, 2, '-5'),
(10, 7, 4, '-4'),
(11, 8, 10, 'Je mag het speelbord niet meer aanschouwen'),
(12, 9, 4, '-2'),
(13, 9, 2, '-1'),
(14, 10, 7, '1'),
(15, 11, 2, '-5'),
(16, 12, 0, '1'),
(17, 12, 9, '2'),
(18, 13, 0, '-5'),
(19, 13, 1, '-5'),
(20, 13, 2, '-5'),
(21, 14, 10, 'Je verdiend het dubbele EXP tijdens de Blood Thirst, maar je verdediging gaat met 50% naar beneden'),
(22, 15, 4, '1'),
(23, 16, 3, '1'),
(24, 17, 10, 'Valstrikken hebben geen Effect'),
(25, 18, 3, '3'),
(26, 19, 4, '-1'),
(27, 20, 10, 'Wissel met een andere speler in de beurtvolgorde'),
(28, 21, 10, 'Verplaats een monster naar keuze in de huidige kamer.'),
(29, 22, 10, 'Elke held in de kamer mag zich eenmaal vrij verplaatsen'),
(30, 23, 3, '4'),
(31, 23, 4, '4'),
(32, 24, 4, '3');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `basic`
--

CREATE TABLE IF NOT EXISTS `basic` (
  `basic_id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `basic`
--

INSERT INTO `basic` (`basic_id`, `name`) VALUES
(0, 'attack'),
(1, 'defence'),
(2, 'walking'),
(3, 'Mana'),
(4, 'Health'),
(5, 'Skillpoints'),
(6, 'Arrows'),
(7, 'Gold Coins'),
(8, 'turn_id'),
(9, 'D20 penalty'),
(10, 'Plain Text Message'),
(11, 'EXP'),
(12, 'EXP Multiplier');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `condition`
--

CREATE TABLE IF NOT EXISTS `condition` (
  `condition_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `condition`
--

INSERT INTO `condition` (`condition_id`, `name`, `duration`) VALUES
(0, 'Healthy', 0),
(1, 'Prone', 2),
(2, 'Surprized', 1),
(3, 'Slowed', 1),
(4, 'Marked', 4),
(5, 'Stunned', 1),
(6, 'Dazed', 1),
(7, 'Poisoned', 3),
(8, 'Blinded', 3),
(9, 'Crippled', 2),
(10, 'Lucky', 1),
(11, 'Immobilized', 5),
(12, 'Overthrown', 3),
(13, 'Dying', 3),
(14, 'Blood Thirst', 4),
(15, 'Healing', 0),
(16, 'Strenthening', 0),
(17, 'Blessed', 5),
(18, 'Strenthening', 0),
(19, 'Poisoned', 1),
(20, 'Blessed', 0),
(21, 'Fortified', 0),
(22, 'Light feeted', 0),
(23, 'Recovering', 0),
(24, 'Healing', 0),
(25, 'Incapacitated', 1),
(26, 'Heavy Strenthening', 0),
(27, 'Rushed', 0),
(28, 'Heavy Healing', 0),
(29, 'Extreme Healing', 0),
(30, 'Extreme Strenthening', 0),
(31, 'Blessed', 0),
(32, 'Blessed', 0),
(33, 'Blessed', 10),
(34, 'Fastened', 2),
(35, 'Holy', 2),
(36, 'Elevated', 5),
(37, 'Cursed', 1),
(38, 'Cursed', 6),
(39, 'Cursed', 0),
(40, 'Cursed', 0),
(41, 'Slightly Fortified Defence', 20),
(42, 'Slighty Fastened Movement', 20),
(43, 'Fortified Defence', 20),
(44, 'Fastened Walking', 20),
(45, 'Strongly Fortyfied Defence', 20),
(46, 'Strongly Fastened Walking', 20),
(47, 'Mana Restoration', 20),
(48, 'Wizard Guide', 20);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `inventory`
--

CREATE TABLE IF NOT EXISTS `inventory` (
  `item_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `type` int(11) NOT NULL COMMENT 'refers to the type table',
  `condition` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `inventory`
--

INSERT INTO `inventory` (`item_id`, `name`, `type`, `condition`) VALUES
(0, 'Huisje van Slak', 1, '0'),
(1, 'Rode Rozenstekkers', 1, '0'),
(2, 'Rotsmos', 2, '0'),
(3, 'Rupsvoorhuid', 2, '0'),
(4, 'Tijdskristal', 3, '0'),
(5, 'Sterrenstof', 3, '0'),
(6, 'Schatkistengruis', 3, '0'),
(7, 'Vergruisde Schedel', 3, '0'),
(8, 'Heksenbloed', 4, '0'),
(9, 'Bloed van pasgeboren babies', 4, '0'),
(10, 'Drakenwimpers', 4, '0'),
(11, 'Vogelkak', 4, '0'),
(12, 'Granietschilfers', 5, '0'),
(13, 'Vulkaanbessen', 5, '0'),
(14, 'Gezegend Putwater', 6, '0'),
(15, 'Schilfers van Blauwe Saffier', 10, '0'),
(16, 'Nectar', 6, '0'),
(17, 'Vergiftigd Putwater', 6, '0'),
(18, 'Rode Grotbloemstuifmeel', 7, '0'),
(19, 'Goudstukschilfers', 7, '0'),
(20, 'Groenrupsfluf', 7, '0'),
(21, 'Wolkenextract', 8, '0'),
(22, 'Arsenicum', 8, '0'),
(23, 'Mitrylrag', 8, '0'),
(24, 'Schimmenschaduw', 9, '0'),
(25, 'Gebroken Drakenhart', 9, '0'),
(26, 'Drakenbot-as', 9, '0'),
(27, 'Kolibrisap', 10, '0'),
(28, 'Drakenschubben', 10, '0'),
(29, 'C6H12O6', 11, '0'),
(30, 'Gemalen Konijnenpoot', 11, '0'),
(31, 'Gedroogde Hennepbloesems', 11, '0'),
(32, 'Arrows', 0, '0'),
(33, 'Gold Coins', 0, '0'),
(34, 'Health-Potion', 0, '15'),
(35, 'Mana-Potion', 0, '16'),
(36, 'Toverdrank van het Kalf en de Put. (Zie kaart.)', 13, '17'),
(37, 'Toverdrank van Minder Herstel.(Zie kaart)', 13, '18'),
(38, 'Geneeskrachtige Toverdrank. (Zie kaart voor uitwerking)', 15, '0'),
(39, 'Drank voor extra tovertalent (Zie kaart voor uitwerking)', 15, '0'),
(40, 'Vergif', 14, '19'),
(41, 'Toverdrank voor Kracht (Zie kaart voor uitwerking)', 15, '0'),
(42, 'Heilig Water (Zie kaart voor uitwerking)', 15, '0'),
(43, 'Drank van Magische onkwetsbaarheid (Zie kaart voor uitwerking)', 15, '0'),
(44, 'Toverdrank voor snelheid (Zie kaart voor uitwerking)', 15, '0'),
(45, 'Versterkende toverdrank (Zie kaart voor uitwerking)', 15, '0'),
(46, 'Heldendrank (Zie kaart voor Uitwerking)', 15, '0'),
(47, 'Toverdrank met de Zegen van Kord. (Zie kaart voor uitwerking)', 15, '0'),
(48, 'Toverdrank van Rookgordijn', 13, '22'),
(49, 'Toverdrank van De Sterke Arm', 13, '21'),
(50, 'Toverdrank van Initiatief', 13, '20'),
(51, 'Toverdrank van Groots Herstel', 13, '23'),
(52, 'Toverdrank van lichte wondgenezing', 13, '24'),
(53, 'Toverdrank van Zwakte (Zie kaart voor uitwerking)', 14, '0'),
(54, 'Toverdrank van akelig gelach', 14, '25'),
(55, 'Toverdrank van Verassingsaanval (Zie kaart voor Uitwerking)', 14, '0'),
(56, 'Toverdrank van Stop De Tijd', 14, '25'),
(57, 'Toverdrank van Herstel', 13, '26'),
(58, 'Toverdrank van Adrenaline', 13, '27'),
(59, 'Toverdrank van matige wondgenezing', 13, '28'),
(60, 'Toverdrank van Grote Zwakte (Zie kaart voor uitwerking)', 14, '0'),
(61, 'Toverdrank van Ernstige Wondgenezing', 13, '29'),
(62, 'Toverdrank Terug in de tijd', 13, '31'),
(63, 'Toverdrank van de genezende Cirkel (Zie kaart voor uitwerking)', 13, '15'),
(64, 'Toverdrank van groots herstel', 13, '30'),
(65, 'Combo-Restoration Potion', 13, '32'),
(66, 'Essence Of Plants', 13, '33'),
(67, 'Toverdrank Der Weerwind', 13, '34'),
(68, 'Mirrage Extract', 13, '35'),
(69, 'Philter of Wisdom\r\n', 13, '36'),
(70, 'Medusa''s Recepy', 14, '37'),
(71, 'Lingering Potion', 14, '38'),
(72, 'Extract of Doom', 14, '39'),
(73, 'Potion From Hell', 14, '40'),
(74, 'VeruungsDrang (Level 1)', 16, '41'),
(75, 'VeruungsDrang (Level 2)', 16, '41;42'),
(76, 'VeruungsDrang (Level 3)', 16, '43;44'),
(77, 'Halfaginer''s Kuras (Level 1)', 17, '41'),
(78, 'Halfaginer''s Kuras (Level 2)', 17, '41;42\r\n'),
(79, 'Halfaginer''s Kuras (Level 3)', 17, '43;44'),
(80, 'Krugas'' Utoq (Level 1)', 18, '42'),
(81, 'Krugas'' Utoq (Level 2)', 18, '41;42'),
(82, 'Krugas'' Utoq (Level 3)', 18, '43;44'),
(83, 'Defilerskiln (Level 1)', 19, '41'),
(84, 'Defilerskiln (Level 2)', 19, '43'),
(85, 'Defilerskiln (Level 3)', 19, '43;44'),
(86, 'Rootserfaxl (Level 1)', 20, '41'),
(87, 'Rootserfaxl (Level 2)', 20, '41;42'),
(88, 'Rootserfaxl (Level 3)', 20, '42;43'),
(89, 'Devine''s Cloak (Level 1)', 21, '0'),
(90, 'Devine''s Cloak (Level 2)', 21, '41'),
(91, 'Devine''s Cloak (Level 3)', 21, '43;44'),
(92, 'Merlin''s Japron (Level 1)', 22, '0'),
(93, 'Merlin''s Japron (Level 2)', 22, '41'),
(94, 'Merlin''s Japron (Level 3)', 22, '45'),
(95, 'Protagonas Pjottr (Level 1)', 23, '41;42'),
(96, 'Protagonas Pjottr (Level 2)', 23, '43;44'),
(97, 'Protagonas Pjottr (Level 3)', 23, '45;46'),
(98, 'Ikraos Halfaagirn (Level 1)', 24, '0'),
(99, 'Ikraos Halfaagirn (Level 2)', 24, '41'),
(100, 'Ikraos Halfaagirn (Level 3)', 24, '45;46'),
(101, 'Refugee''s Cape (Level 1)', 25, '0\r\n'),
(102, 'Refugee''s Cape (Level 2)', 25, '41;42\r\n'),
(103, 'Refugee''s Cape (Level 3)', 25, '43;44'),
(104, 'Cupper Curas (Level 1)', 26, '41'),
(105, 'Cupper Curas (Level 2)', 26, '43'),
(106, 'Cupper Curas (Level 3)', 26, '45'),
(107, 'Mitryl Matriach (Level 1)', 26, '41;42'),
(108, 'Mitryl Matriach (Level 2)', 26, '43;44'),
(109, 'Mitryl Matriach (Level 3)', 26, '45;46'),
(110, 'Yeti Armor (Level 1)', 27, '41;3'),
(111, 'Yeti Armor (Level 2)', 27, '41;42'),
(112, 'Yeti Armor (Level 3)', 27, '43;42'),
(113, 'Shammaan Armor (Level 1)', 27, '47'),
(114, 'Shammaan Armor (Level 2)', 27, '48'),
(115, 'Speer', 28, '0'),
(116, 'Kruisboog', 28, '0'),
(117, 'Handboog van de ouden', 28, '0'),
(118, 'Heilige Elfenboog', 28, '0'),
(119, 'Kruisboog van het geloof', 28, '0'),
(120, 'Boog van onafhankelijkheid', 28, '0'),
(121, 'Boog van Phoenix', 28, '0'),
(122, 'Heilige kruisboog van Pelor', 28, '0'),
(123, 'Composiet Boog', 28, '0'),
(124, 'Grote Handboog van de Elfenvorsten', 28, '0'),
(125, 'Medusa''s Verstenende boog', 28, '0'),
(126, 'Schreeuwende Dodenelf', 28, '0'),
(127, 'De Verlosser', 28, '0'),
(128, 'Toorn van de Draak', 28, '0'),
(129, 'Kort Zwaard', 28, '0'),
(130, 'Vuistbijl', 28, '0'),
(131, 'Strijdbijl', 28, '0'),
(132, 'Eenhandig Slagzwaard', 28, '0'),
(133, 'Degen van de verbannen Koningen', 28, '0'),
(134, 'De meesterbijl', 28, '0'),
(135, 'De bottensplijter', 28, '0'),
(136, 'Goedendag van het geloof', 28, '0'),
(139, 'goedendag van de verlossing', 28, '0'),
(140, 'Schedelsplijter', 28, '0'),
(141, 'Doodsbrenger', 28, '0'),
(142, 'De Ongehoorzame Dienaar van Kord', 28, '0'),
(143, 'Slagzwaard', 28, '0'),
(144, 'Staf', 28, '0'),
(145, 'Tweehandig Slagzwaard', 28, '0'),
(146, 'Trouwe aks van het dwergenvolk', 28, '0'),
(147, 'Hamer van Vrijheid', 28, '0'),
(149, 'Folterzwaard van Slavernij', 28, '0\r\n'),
(150, 'Grootzwaard', 28, '0'),
(151, 'Hamer van Onderwerping', 28, '0'),
(152, 'Hellebaard', 28, '0'),
(153, 'Kords bijl van Woede', 28, '0'),
(154, 'Pijnbrenger', 28, '0'),
(155, 'Werpmes', 28, '0'),
(157, 'Flitskogel', 28, '0'),
(159, 'Giftige Blaaspijp', 28, '0'),
(160, 'Handschoen van Lijden', 28, '0'),
(161, 'Kwelboor', 28, '0'),
(162, 'Bomsterren', 28, '0'),
(163, 'Medogenloze Wurg-Ster', 28, '0'),
(164, 'Energieschild', 28, '0'),
(165, 'Onzichtbare Dienaar', 28, '0'),
(166, 'Godsdienstig Wapen', 28, '0'),
(167, 'Dichte mist', 28, '0'),
(168, 'Gebalde Reuzenvuist', 28, '0'),
(169, 'Minder Genezende Cirkel', 28, '0'),
(170, 'Groots Herstel', 28, '0'),
(171, 'Genezende Cirkel', 28, '0'),
(172, 'Vuurpijl', 28, '0'),
(173, 'Melf''s Zuurpijl', 28, '0'),
(174, 'Vriesstraal', 28, '0'),
(175, 'Krachtkooi', 28, '0'),
(178, 'Magische Stralen', 28, '0'),
(179, 'Vurige Handen', 28, '0'),
(180, 'Kettingbliksem', 28, '0'),
(181, 'Bliksemschicht', 28, '0'),
(182, 'Kegel van Kou', 28, '0'),
(183, 'Vuurbal', 28, '0'),
(184, 'Vuurbal', 28, '0'),
(185, 'Ijsstorm', 28, '0'),
(186, 'Vuurstorm', 28, '0'),
(187, 'Vuurzee', 28, '0');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `monsters`
--

CREATE TABLE IF NOT EXISTS `monsters` (
  `monster_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `multiplier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `monsters`
--

INSERT INTO `monsters` (`monster_id`, `name`, `multiplier`) VALUES
(0, 'Schim', 1),
(1, 'Kobold Heroquest', 2),
(2, 'Kobold D&D', 3),
(3, 'Ork', 4),
(4, 'Skelet D&D', 5),
(5, 'Skelet Heroquest', 6),
(6, 'Gnoll', 7),
(7, 'Slijk', 8),
(8, 'Zombie', 9),
(9, 'Mummie', 10),
(10, 'Menseneter', 11),
(11, 'Krengtor', 12),
(12, 'Bullebijter', 13),
(13, 'Fimir', 14),
(14, 'Dooie Pier', 15),
(15, 'Gamut', 16),
(16, 'Krijger Van Chaos', 17),
(17, 'Troll', 18),
(18, 'Lijkenpikker', 19),
(19, 'Dienaars van het Wezen', 20);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
`permission_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `permission`
--

INSERT INTO `permission` (`permission_id`, `type`, `name`) VALUES
(1, 0, 'Gebruiker'),
(2, 1, 'Administrator');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `races`
--

CREATE TABLE IF NOT EXISTS `races` (
  `race_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `attack` int(11) NOT NULL,
  `defence` int(11) NOT NULL,
  `walking` int(11) NOT NULL,
  `intelligence` int(11) NOT NULL,
  `health` int(11) NOT NULL,
  `available_skills` text NOT NULL COMMENT 'refers to the table skills, divided by ;'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `races`
--

INSERT INTO `races` (`race_id`, `name`, `attack`, `defence`, `walking`, `intelligence`, `health`, `available_skills`) VALUES
(0, 'Human', 1, 1, 1, 3, 3, '1;2;3;4;5;6;7;8;9;10;11;12;26;27;33;19;28'),
(1, 'Elf', 1, 1, 1, 4, 2, '1;2;3;4;5;6;7;8;9;10;11;12;26;19;28;29;33;13;15;16;21'),
(2, 'Dwarf', 1, 1, 0, 2, 4, '1;2;3;4;5;6;7;8;9;10;11;12;17;30;23'),
(3, 'Dragonborn', 1, 1, 1, 3, 4, '1;2;3;4;5;6;7;8;9;10;11;12;17;26'),
(4, 'Halfling', 1, 1, 1, 2, 3, '1;2;3;4;5;6;7;8;9;10;11;12;16;18;28;27;29'),
(5, 'Warrior', 2, 1, 1, 0, 4, '17;18;19;22;23'),
(6, 'Rogue', 1, 1, 1, 2, 2, '15;16;18;19;14;27;28;29;31'),
(7, 'Mage', 0, 1, 1, 3, 1, '24;25;13;14;26'),
(8, 'Cleric', 1, 1, 1, 2, 2, '23;24;34;13;32;33'),
(9, 'Palladin', 1, 1, 1, 1, 2, '17;18;15;19;20;22;23;32;28');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `shop`
--

CREATE TABLE IF NOT EXISTS `shop` (
  `item_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL COMMENT 'refers to the table types',
  `price_value` text NOT NULL COMMENT 'gives the value for price_item',
  `price_item` text NOT NULL COMMENT 'refers to the table inventory. Divided by ;',
  `skill_value` text NOT NULL COMMENT 'gives the value for the skill_requirement',
  `skill_requirement` text NOT NULL COMMENT 'refers to the skill table. Divided by;',
  `upgrade` int(11) NOT NULL COMMENT 'Refers to the Shop database'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `shop`
--

INSERT INTO `shop` (`item_id`, `inventory_id`, `price_value`, `price_item`, `skill_value`, `skill_requirement`, `upgrade`) VALUES
(0, 34, '1;1;1', '0;14;18', '1', '13', 0),
(1, 34, '50', '33', '0', '0', 0),
(2, 35, '1;1;1', '14;12;18', '1', '13', 0),
(3, 35, '50', '33', '0', '0', 0),
(4, 36, '2;2;1', '14;12;18', '1', '13', 0),
(5, 37, '3;3;2', '2;14;18', '1', '13', 0),
(6, 65, '2;1;1;2;1', '14;0;2;18;20', '2', '13', 0),
(7, 37, '4;4;1;8', '2;14;16;18', '2', '13', 0),
(8, 52, '3;1;4;3', '0;19;14;18', '2', '13', 0),
(9, 64, '5;5;5;2', '0;2;16;26', '2', '13', 0),
(10, 50, '4;1;1;2', '13;21;31;5', '2', '13', 0),
(11, 48, '2;5;1;2', '12;6;23;29', '3', '13', 0),
(12, 49, '2;1;1;5', '8;10;25;18', '3', '13', 0),
(13, 51, '5;4;2;2;1;12', '2;14;29;16;26;18', '3', '13', 0),
(14, 59, '4;3;1;3;4;10', '0;13;27;20;16;18', '3', '13', 0),
(15, 44, '2;2;1;3', '16;18;30;18', '4', '13', 0),
(16, 58, '2;4;1', '13;9;25', '4', '13', 0),
(17, 63, '4;3;1;5', '16;18;30;0', '4', '13', 0),
(18, 62, '3;3;2;1', '5;6;12;24', '4', '13', 0),
(19, 56, '1;2;5;1;3', '28;5;6;7;8', '5', '13', 0),
(20, 61, '1;1;5;5;1', '30;27;16;14;33', '5', '13', 0),
(21, 40, '1;1', '17;22', '1', '14', 0),
(22, 54, '2;1;1;1', '7;9;17;19', '2', '14', 0),
(23, 53, '2;1;2;1;4', '17;22;11;23;1', '2', '14', 0),
(24, 55, '2;5;3;1', '1;17;12;26', '4', '14', 0),
(25, 60, '5;2;1;7', '3;27;30;20', '5', '14', 0),
(26, 66, '2;2;3;1', '14;16;20;24', '3', '13', 0),
(27, 67, '1;1;1;1;1', '5;9;15;30;31', '4', '13', 0),
(28, 68, '10;15;1;1', '12;14;21;15', '4', '13', 0),
(29, 69, '3;1;4;1;3', '29;15;13;10;20', '5', '13', 0),
(30, 70, '1;4;2;3;1', '7;16;29;19;24', '3', '14', 0),
(31, 71, '3;1;2;4;2', '1;0;23;11;6', '4', '14', 0),
(32, 72, '3;3;1;4', '7;8;9;26', '4', '14', 0),
(33, 73, '1;3;4;5;6', '7;28;11;22;9', '5', '14', 0),
(34, 74, '100', '33', '1', '18', 35),
(35, 75, '500', '33', '1', '18', 36),
(36, 76, '1000', '33', '1;1', '18;19', 0),
(37, 77, '100', '33', '1', '23', 38),
(38, 78, '500', '33', '1', '23', 39),
(39, 79, '1000', '33', '1;1', '23;3', 0),
(40, 80, '100', '33', '1', '4', 41),
(41, 81, '500', '33', '1', '4', 42),
(42, 82, '1500', '33', '1', '4', 0),
(43, 83, '100', '33', '1', '8', 44),
(44, 84, '600', '33', '1;1', '8;3', 45),
(45, 85, '1200', '33', '1;1;1', '8;3;20', 0),
(46, 86, '100', '33', '1', '9', 47),
(47, 87, '500', '33', '1', '9', 48),
(48, 88, '1000', '33', '2', '9', 0),
(49, 89, '200', '33', '0', '0', 50),
(50, 90, '750', '33', '0', '0', 51),
(51, 91, '1200', '33', '0', '0', 0),
(52, 92, '300', '33', '0', '0', 53),
(53, 93, '600', '33', '0', '0', 54),
(54, 94, '2000', '33', '0', '0', 0),
(55, 95, '300', '33', '0', '0', 56),
(56, 96, '1000', '33', '0', '0', 57),
(57, 97, '1000', '33', '1', '22', 0),
(58, 98, '50', '33', '0', '0', 59),
(59, 99, '500', '33', '0', '0', 60),
(60, 100, '5000', '33', '0', '0', 0),
(61, 101, '200', '33', '0', '0', 62),
(62, 102, '800', '33', '0', '0', 63),
(63, 103, '1250', '33', '0', '0', 0),
(64, 104, '400', '33', '0', '0', 65),
(65, 105, '800', '33', '0', '0', 66),
(66, 106, '1400', '33', '0', '0', 0),
(67, 107, '500', '33', '1;1', '6;7', 68),
(68, 108, '1000', '33', '1;2', '6;7', 69),
(69, 109, '1500', '33', '1;2', '6;7', 0),
(71, 111, '375;1', '33;110', '0', '0', 72),
(72, 112, '750', '33', '0', '0', 0),
(73, 113, '1000', '33', '1;', '0;1', 74),
(74, 114, '2000', '33', '1;1;1', '0;1;2', 0),
(75, 115, '135', '33', '0', '0', 0),
(76, 116, '300', '33', '0', '0', 0),
(77, 117, '40', '33', '0', '0', 0),
(78, 118, '70', '33', '0', '0', 0),
(79, 119, '50', '33', '0', '0', 0),
(80, 120, '90', '33', '0', '0', 0),
(81, 121, '80', '33', '0', '0', 0),
(82, 122, '200', '33', '0', '0', 0),
(83, 123, '175', '33', '0', '0', 0),
(84, 124, '200', '33', '0', '0', 0),
(85, 125, '275', '33', '0', '0', 0),
(86, 126, '250', '33', '0', '0', 0),
(87, 127, '300', '33', '0', '0', 0),
(88, 128, '1000000', '33', '0', '0', 0),
(89, 129, '300', '33', '0', '0', 0),
(90, 130, '250', '33', '0', '0', 0),
(91, 131, '750', '33', '0', '0', 0),
(92, 132, '100', '33', '0', '0', 0),
(93, 133, '125', '33', '0', '0', 0),
(94, 134, '175', '33', '0', '0', 0),
(95, 135, '200', '33', '0', '0', 0),
(96, 136, '125', '33', '0', '0', 0),
(99, 139, '300', '33', '0', '0', 0),
(100, 140, '350', '33', '0', '0', 0),
(101, 141, '600', '33', '0', '0', 0),
(102, 142, '10000000', '33', '0', '0', 0),
(103, 143, '500', '33', '0', '0', 0),
(104, 144, '100', '33', '0', '0', 0),
(105, 145, '200', '33', '0', '0', 0),
(106, 146, '250', '33', '0', '0', 0),
(107, 147, '300', '33', '0', '0', 0),
(109, 149, '175', '33', '0', '0', 0),
(110, 150, '300', '33', '0', '0', 0),
(111, 151, '350', '33', '0', '0', 0),
(112, 152, '475', '33', '0', '0', 0),
(113, 153, '500', '33', '0', '0', 0),
(114, 154, '10000000', '33', '0', '0', 0),
(115, 155, '15', '33', '0', '0', 0),
(117, 157, '50', '33', '0', '0', 0),
(119, 159, '100', '33', '0', '0', 0),
(120, 160, '200', '33', '0', '0', 0),
(121, 161, '300', '33', '0', '0', 0),
(122, 162, '125', '33', '0', '0', 0),
(123, 163, '150', '33', '0', '0', 0),
(124, 164, '1000000', '33', '0', '0', 0),
(125, 165, '25', '33', '0', '0', 0),
(126, 166, '100', '33', '0', '0', 0),
(127, 167, '100', '33', '0', '0', 0),
(128, 168, '115', '33', '0', '0', 0),
(131, 171, '150', '33', '0', '0', 0),
(132, 172, '40', '33', '0', '0', 0),
(133, 173, '75', '33', '0', '0', 0),
(134, 174, '50', '33', '0', '0', 0),
(135, 175, '100', '33', '0', '0', 0),
(136, 186, '350', '33', '0', '0', 0),
(138, 178, '50', '33', '0', '0', 0),
(139, 179, '50', '33', '0', '0', 0),
(140, 180, '50', '33', '0', '0', 0),
(141, 181, '200', '33', '0', '0', 0),
(142, 182, '175', '33', '0', '0', 0),
(143, 183, '200', '33', '0', '0', 0),
(144, 184, '175', '33', '0', '0', 0),
(145, 185, '250', '33', '0', '0', 0),
(177, 187, '100000000', '33', '0', '0', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `skills`
--

CREATE TABLE IF NOT EXISTS `skills` (
  `skill_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `type` text NOT NULL,
  `subtype` text NOT NULL,
  `levels` text NOT NULL COMMENT '# of levels',
  `level_advantages` text NOT NULL COMMENT 'explanation of each level, divided by ;'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `skills`
--

INSERT INTO `skills` (`skill_id`, `name`, `type`, `subtype`, `levels`, `level_advantages`) VALUES
(0, 'Destruction Magic', 'General Skill', 'Magic', '3', 'Mogelijkheid om Destruction Magic spreuken van niveau 1 aan te kopen.;Mogelijkheid om Destruction Magic spreuken van niveau 2 aan te kopen.;Mogelijkheid om Destruction Magic spreuken van niveau 3 aan te kopen.'),
(1, 'Restoration Magic', 'General Skills', 'Magic', '3', 'Mogelijkheid om Restoration Magic spreuken van niveau 1 aan te kopen.;Mogelijkheid om Restoration Magic spreuken van niveau 2 aan te kopen.;Mogelijkheid om Restoration Magic spreuken van niveau 3 aan te kopen.'),
(2, 'Conjuration Magic', 'General Skills', 'Magic', '3', 'Mogelijkheid om Conjuration Magic spreuken van niveau 1 aan te kopen.;Mogelijkheid om Conjuration Magic spreuken van niveau 2 aan te kopen.;Mogelijkheid om Conjuration Magic spreuken van niveau 3 aan te kopen.'),
(3, 'One-Handed Weapons', 'General Skills', 'Combat', '3', 'Mogelijkheid om One-handed weapons van niveau 1 aan te kopen.;Mogelijkheid om One-handed weapons van niveau 2 aan te kopen.;Mogelijkheid om One-handed weapons van niveau 3 aan te kopen.'),
(4, 'Two-Handed Weapons', 'General Skills', 'Combat', '3', 'Mogelijkheid om Two-handed weapons van niveau 1 aan te kopen.;Mogelijkheid om Two-handed weapons van niveau 2 aan te kopen.;Mogelijkheid om Two-handed weapons van niveau 3 aan te kopen.'),
(5, 'Ranged Weapons', 'General Skills', 'Combat', '3', 'Mogelijkheid om Ranged weapons van niveau 1 aan te kopen.;Mogelijkheid om Ranged weapons van niveau 2 aan te kopen.;Mogelijkheid om Ranged weapons van niveau 3 aan te kopen.'),
(6, 'Perception', 'General Skills', 'Dungeoneering', '1', 'Dit laat het maken van een Perception Check toe.'),
(7, 'Arcana', 'General Skills', 'Dungeoneering', '2', 'Dit laat toe potions te drinken van level 2 of minder;Dit laat toe alle potions te drinken die er te vinden zijn.'),
(8, 'Intimidation', 'General Skills', 'Social', '5', 'Dit level geeft de mogelijkheid tot Intimidation conversatieopties. Gooi 1x D4 als bepalende factor, 1 doorslaggevend nummer, vooraf afgesproken met de DM. Je kan enkel je eigen ras intimideren;Je kan nu alle rassen intimideren.;Gooi 2x D4 als bepalende factor.;2 Doorslaggevende nummers vooraf afgesproken met de DM.;Gooi 3x D4 als bepalende factor.'),
(9, 'Stealth Weapons', 'General Skill\r\n', 'Combat', '2', 'Mogelijkheid om Stealth weapons van niveau 2 aan te kopen.;Mogelijkheid om Stealth weapons van niveau 3 aan te kopen.'),
(10, 'Persuation', 'General Skills', 'Social', '5', 'Dit level geeft de mogelijkheid tot Persuation conversatieopties. Gooi 1x D4 als bepalende factor, 1 doorslaggevend nummer, vooraf afgesproken met de DM. Je kan enkel je eigen ras overtuigen.;Je kan nu alle rassen overtuigen.; Gooi 2x D4 als bepalende factor.;2 Doorslaggevende nummers vooraf afgesproken met de DM;Gooi 3x D4 als bepalende factor'),
(11, 'Renegade', 'General Skills', 'Social', '6', 'Dit level geeft de mogelijkheid om Renegade conversatieopties vrij te spelen. Je kan level 1 renegadeopties gebruiken. Zie 9.5.4.;Je kan level 2 renegadeopties gebruiken. Zie 9.5.4.;Je kan level 3 renegadeopties gebruiken. Zie 9.5.4.;Je kan level 4 renegadeopties gebruiken. Zie 9.5.4.;Je kan level 5 renegadeopties gebruiken. Zie 9.5.4.;Je kan level 6 renegadeopties gebruiken. Zie 9.5.4.'),
(12, 'Paragon', 'General Skills', 'Paragon', '6', 'Dit level geeft je de mogelijkheid tot het gebruiken van Paragonopties in een conversatie. Je hebt nu Paragon level 1. Zie 9.5.4.;Je hebt nu Paragon Level 2. Zie 9.5.4.;Je hebt nu Paragon Level 3. Zie 9.5.4.;Je hebt nu Paragon Level 4. Zie 9.5.4.;Je hebt nu Paragon Level 5. Zie 9.5.4.;Je hebt nu Paragon Level 6. Zie 9.5.4.'),
(13, 'Herbalism', 'Special Skills', 'Magic', '10', 'Dit level opent de opties voor Herbalism. Je kan nu alle niveau 1 Herbalism potions maken. Zie 4.1.1 Alleen jij kan je gemaakte potions gebruiken;Je kan je gemaakte potions nu doorgeven aan teamgenoten.;Alle gemaakte potions herstellen 1 levenspunt meer.;Je kan nu alle niveau 2 Herbalism potions maken. Zie 4.1.1;Je kan nu gemaakte potions verkopen aan de DM te allen tijde.;Je kan nu alle niveau 3 Herbalism potions maken. Zie 4.1.1;Alle gemaakte potions herstellen 1 manapunt meer.;Je kan nu alle niveau 4 Herbalism potions maken. Zie 4.1.1;Alles dat je maakt, maak je nu dubbel voor dezelfde prijs;Je kan nu alle niveau 5 Herbalism potions maken. Zie 4.1.1'),
(14, 'Posion Making', 'Special Skills', 'Magic', '10', 'Dit level opent de opties voor Poison making. Je kan alle niveau 1 poisonous Potions maken. Zie 4.1.2. Enkel jij kan de potions gebruiken op andere wezens.\r\n;Iedereen kan je potions gebruiken op andere wezens.;Je kan alle niveau 2 poisonous Potions maken. Zie 4.1.2.;Alle gemaakte potions zijn dubbel zo schadelijk.;Je kan alle niveau 3 poisonous Potions maken. Zie 4.1.2.;Alle gemaakte potions kosten nu de helft minder ingrediënten.;Je kan alle niveau 4 poisonous Potions maken. Zie 4.1.2.;Je kan alle niveau 5 poisonous Potions maken. Zie 4.1.2.;Je potions werken dubbel zo lang in op het slachtoffer.;Je maakt dubbel zoveel potions voor dezelfde prijs.'),
(15, 'Marksman', 'Special Skills', 'Combat', '10', 'Dit level maakt pijlen dodelijker, maar verandert nog niets.\r\nD20 – 5 om critical hit te bepalen Deze skill is niet combineerbaar met combo-aanvallen;D20 – 4 om critical hit te bepalen;Deze skill is wel combineerbaar met combo-aanvallen;D20 – 3 om critical hit te bepalen;D20 – 2 om critical hit te bepalen;Je pijl doet dubbel zoveel schade voor doelwitten verder dan 4 vakjes van je.;D20 – 1 om critical hit te bepalen;D20 om critical hit te bepalen;D20 + 1 om critical hit te bepalen;D20 + 2 om critical hit te bepalen'),
(16, 'Ranger', 'Special Skills', 'Combat', '15', 'Dit level maakt sneller pijlen schieten mogelijk, maar veranderd nog niets.\r\nD8 = #vakjes ver om bereik te weten\r\nDeze skill is niet combineerbaar met combo-aanvallen.;D8 + 1 om bereik te bepalen;D8 + 2 om bereik te bepalen;Indien je je shot mist, mag je nogmaals schieten in dezelfde beurt.;Deze skill is wel combineerbaar met combo-aanvallen;Je mag altijd 2x schieten in 1 beurt;D8 + 4 om bereik te bepalen;Je mag 2 doelwitten tegelijk raken met 1 shot.;D8 x 2 om bereik te bepalen.;Indien het schot gemist wordt, bewaar je toch je pijl.;Indien het doelwit verder dan 5 vakjes van je verwijderd is, mag je een derde keer schieten in 1 beurt.;Je kan elk doelwit in je gezichtsveld raken, hoe ver deze ook van je verwijderd is.;Indien je schot geraakt heeft, moet de vijand zijn volgende beurt overslaan.;Gooi D4. Indien 2 heb je onmiddellijk een critical hit ALS je pijl zijn doelwit raakt.;Indien het shot gemist is, doe je toch 50% schade.'),
(17, 'Souvereign', 'Special Skills', 'Combat', '10', 'Dit level maakt One- en Two- handed weapons dodelijker, maar veranderd nog niets. D20 om critical hit te bepalen Deze skill is niet combineerbaar met combo-aanvallen Bij dual weapons moet de D20 geworpen worden voor elk wapen.; D20 + 1 om critical hit te bepalen;D20 + 2 om critical hit te bepalen;Deze skill is combineerbaar met combo-aanvallen.;D20 + 3 om critical hit te bepalen;Deze skill hoeft maar 1 keer gedaan worden bij Dual-weapons. ;One-handed weapons doen 1 levenspunt meer schade;Two-handed weapons doen 1 levenspunt meer schade;D20 + 5 om critical hit te bepalen;Je wapen is 1x per aanvalsequentie dubbel zo krachtig.'),
(18, 'Dual-Wield Training', 'Special Skills', 'Combat', '5', 'Dual-Wield is mogelijk. Het tweede wapen doet maar de helft van de gegooide schade.\r\nDeze skill is niet combineerbaar met combo-aanvallen;Het tweede wapen doet 1 aanvalspunt minder schade.;Het tweede wapen is even schadelijk als het normale wapen.;Deze skill is combineerbaar met combo-aanvallen;Indien 1 wapen een critical hit heeft, zal het andere wapen ook automatisch een critical hit veroorzaken.'),
(19, 'Archery Training', 'Special Skills', 'Combat', '5', 'Archery is mogelijk. De speler kan tot 10 pijlen bijhouden.;De speler kan tot 20 pijlen bijhouden.;Je kan over andere spelers heen schieten.;De speler kan tot 50 pijlen bijhouden.;Als elite-boogschutter ben je zelf immuun geworden voor pijlen.'),
(20, 'Warden-Veteran', 'Special Skills', 'Combat', '10', 'Door deze skill zijn combo-aanvallen mogelijk. Dit level veranderd niets, maar laat toe dat combo aanvallen gebeuren.\r\nJe kan Niveau 1 combo-aanvallen doen.\r\nCombo-aanval kost evenveel manapunten als schadepunten dat de aanval vereist.;Je kan Niveau 2 combo-aanvallen uitvoeren;Combo-aanval kost half zoveel manapunten als schadepunten;Je kan Niveau 3 combo-aanvallen uitvoeren;Elke comboaanval kost 2 manapunten;Je kan Niveau 4 combo-aanvallen uitvoeren.;De comboaanval doet +1 schadepunt als voorheen;Je kan Niveau 5 combo-aanvallen uitvoeren.;Een comboaanval kost geen manapunten meer.;De comboaanval doet dubbel zoveel schade aan.'),
(21, 'Warden-Shadow', 'Special Skill', 'Combat', '5', 'Dit level maakt het mogelijk om in deze klasse te levellen, het veranderd niets.  1 comboaanval per aanvalssequentie.;Een comboaanval zorgt ervoor dat je vijand een beurt moet overslaan.;Per beurt kan je nu een combo aanval doen.; Dit is een bufferlevel.;Je kan per beurt zowel een combo aanval doen met een gewoon wapen EN nog eens met een ranged wapen.'),
(22, 'Survivalist', 'Special Skills', 'combat', '4', 'Vanaf nu kan je aanvallen zonder wapen. Je kan 1 Heroquest aanvalsdobbelsteen gebruiken.;Je kan aanvallen zonder wapen met 2 Heroquest dobbestenen.;Je aanval doet sowieso 1 schedeltje aanval;Je unarmed aanval is altijd 2 schedeltjes, je moet geen dobbelsteen meer werpen.'),
(23, 'Endurance', 'Special Skills', 'Combat', '10', 'De speler kan indien hij nog maar 1 levenspunt meer bezit, de volgende aanval ontwijken indien hij bij de D4 een 2 gooit.;De speler heeft 1 levenspunt meer dan aangegeven op de character sheet.;Indien de speler een Critical hit gooit, wordt zijn leven volledig aangevult.;De speler heeft 1 levenspunt en 1 manapunt meer dan aangegeven op de character sheet.;Indien de speler een Critical hit gooit, wordt zijn leven EN zijn manapunten volledig aangevuld.;De speler heeft 2 manapunten meer dan aangegeven op de Character sheet.;De speler kan 1x per dungeon levenspunten ontrekken van zijn mede teamspeler en zo zichzelf genezen.;Dit is een bufferlevel;De speler heeft 2 levenspunten en 1 manapunt meer dan aangegeven op de character sheet.;De speler heeft 2 levenspunten en 2 manapunten meer dan aangegeven op de character sheet.'),
(24, 'Enchantist', 'Special Skills', 'Combat', '10', 'Iedere spreuk die meer dan 6 manapunten vraagt, kost 1 manapunt minder.;Per aanvalsronde waar de speler geen schade oploopt, krijgt hij 1 manapunt bij.;Iedere spreuk die meer dan 5 manapunten vraagt, kost 1 manapunt minder.;Het consumeren van toverdranken die meer manapunten geven,			 krijgt de speler dubbel zoveel mana als dat hij anders zou krijgen.;Iedere spreuk die meer dan 4 manapunten vraagt, kost 1 manapunt minder.;Per aanvalsronde waar de speler geen schade oploopt, krijgt hij 2 manapunten bij.;Iedere spreuk die meer dan 3 manapunten vraagt, kost 1 manapunt minder.;Dit is een bufferlevel.;Iedere spreuk kost 1 manapunt minder.;Bij het drinken van een manapotion kan de speler een gratis spreuk naar keuze casten. (bij spreuken die meer dan 4 manapunten vragen moeten er 2 manapotions gedronken worden.)'),
(25, 'Wizardry', 'Special Skills', 'Combat', '5', 'De speler mag 2x de D20 gooien om kans te maken op de critical hit. De beste worp telt.;Destruction spells doen vanaf nu 1 schedeltje of 1 zwaardje meer schade.;Conjuration spells doen vanaf nu 1 schedeltje of 1 zwaarde meer schade.;Destruction Spells zijn dubbel zo schadelijk;Destruction Spells EN conjuration spells zijn dubbel zo schadelijk'),
(26, 'Insight', 'Special Skills', 'Dungeoneering', '6', 'Je mag 1x gooien met de raderdobbelsteen.;Je mag 2x gooien met de raderdobbelsteen;Indien onschadelijk maken mislukt, ben je geen levenspunt meer kwijt.;Je mag 3x gooien met de raderdobbelsteen;Indien een val zich in 1 vakje radius begint, zie je het. (niet al bewegend.);Je kan niet meer mislukken in het onschadelijk maken van vallen.'),
(27, 'Thievery', 'Special Skills', 'Dungeoneering', '5', 'Je kan medepersonages en npc’s bestelen. Spreek af met DM over de Check die nodig is voor het succesvol bestelen.;Je krijgt 25% meer goud voor goederen die je verkoopt.;Je vind dubbel zoveel goud in kisten.;Je kan vijanden bestelen voor de aanval begint.;Je kan gesloten deuren openen met een voorafgesproken Check.'),
(28, 'Sneak', 'Special Skills', 'Dungeoneering', '10', 'Gooi met een dobbelsteen en haal een bepaald getal om te bepalen ofdat de sneakattempt gelukt is. Ook het maximum aantal vijanden speelt een rol. Het succes van een sneak aanval is respectievelijk met het level van deze skill. D20 – 1 bepalend getal af te spreken met de DM – max 1 vijand.; D20 – 1 bepalend getal af te spreken met de DM – max 2 vijanden.;D20 – 2 bepalende getallen af te spreken met de DM – max 2 vijanden.;\r\nD8 – 2 bepalende getallen af te spreken met de DM – max 2 vijanden.;D8 – 2 bepalende getallen af te spreken met de DM – max 4 vijanden.;D6 – 2 bepalende getallen af te spreken met de DM – max 4 vijanden.;D6 – 3 bepalende getallen af te spreken met de DM – max 4 vijanden.'),
(29, 'Agility', 'Special Skills', 'Dungeoneering', '10', 'Gooi met een dobbelsteen en haal een bepaald getal om te bepalen ofdat de ontwijkattempt gelukt is. Voldoe aan een quota. Indien #schadepunten van de te ontwijken aanval groter is dan D20/20 dan is het ontwijken mislukt Ontwijken is enkel mogelijk bij Ranged aanvallen. Getallen afronden naar beneden.;Ontwijken gaat nu bij alle aanvallen, en niet enkel bij een ranged aanval.;#schadepunten > D20/10;Vanaf nu rond af naar boven.;#schadepunten > D12/6; #schadepunten > D20/5;Dit is een bufferniveau;# schadepunten > D6/2;Dit is een bufferniveau;#schadepunten > D4'),
(30, 'Runecrafting', 'Special Skills', 'Dungeoneering', '5', 'Vanaf nu zijn alle opdrachtdeuren in de dungeon onmiddellijk zichtbaar;Vanaf nu zijn alle deuren in de dungeon onmiddellijk zichtbaar;Vanaf nu zijn alle schatkisten in de dungeon onmiddellijk zichtbaar;Vanaf nu zijn alle valstrikken in de dungeon onmiddellijk zichtbaar;Vanaf nu zijn alle meubels in de dungeon onmiddellijk zichtbaar.'),
(31, 'Reconing', 'Special Skills', 'Dungeoneering', '5', 'Kies bij het begin van de dungeon 1 kamer die zichtbaar wordt.;Vanaf nu zijn alle magische vallen  onmiddellijk zichtbaar.;Vanaf nu zijn de vijanden in de volgende kamer zichtbaar.;Vanaf nu zijn verborgen hendels zichtbaar;Kies bij het begin van de dungeon 2 kamers die zichtbaar worden.'),
(32, 'Bartering', 'Special Skills', 'Social', '10', 'Je krijgt bij aankoop 5% korting;Je krijgt bij aankoop 10% korting;Je krijgt bij aankoop 15% korting;Je krijgt bij aankoop 20% korting;Je krijgt bij aankoop 25% korting;Je krijgt bij aankoop 30% korting;Je krijgt bij aankoop 35% korting;Je krijgt bij aankoop 40% korting;Je krijgt bij aankoop 45% korting;Je krijgt bij aankoop 50% korting'),
(33, 'Reasoning', 'Special Skills', 'Social', '10', 'Je kan vijanden van level 2 of minder proberen overtuigen.;Je kan vijanden van level 4 of minder proberen overtuigen.;Je kan vijanden van level 6 of minder proberen overtuigen.;Je kan vijanden van level 8 of minder proberen overtuigen.;Je kan vijanden van level 10 of minder proberen overtuigen.;Je kan vijanden van level 12 of minder proberen overtuigen.;Je kan vijanden van level 14 of minder proberen overtuigen.;Je kan vijanden van level 16 of minder proberen overtuigen.;Je kan vijanden van level 18 of minder proberen overtuigen.;Je kan vijanden van level 20 of minder proberen overtuigen.'),
(34, 'Woo-loo-loo-loo', 'Special Skills', 'Combat', '20', 'Vijandige wezens van level 1 kunnen voor 1 beurt omgeconverteerd worden.;Vijandige wezens van level 2 kunnen voor 1 beurt omgeconverteerd worden.;Vijandige wezens van level 2 kunnen voor 2 beurten omgeconverteerd worden.;Level 3 voor 2 beurten;Level 4 voor 2 beurten;Level 5 voor 2 beurten;Level 5 voor 3 beurten;Level 6 voor 3 beurten;Level 7 voor 3 beurten;Omgeconverteerde wezens krijgen 1 levenspunt schade als ze uit conversie gaan.;Tijdens de conversie zullen wezens zich niet meer verdedigen tijdens aanvallen.;Level 8 voor 3 beurten;Level 9 voor 3 beurten;Level 10 voor 3 beurten;Level 10 voor 4 beurten;Level 11 voor 4 beurten;Level 12 voor 4 beurten;Level 13 voor 4 beurten;Level 13 voor 5 beurten;Conversie van wezens kost de helft van de manapunten minder.');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `timestamps`
--

CREATE TABLE IF NOT EXISTS `timestamps` (
  `timestamp_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `basic_timestamp` varchar(16) NOT NULL,
  `skill_timestamp` varchar(16) NOT NULL,
  `inventory_timestamp` varchar(16) NOT NULL,
  `condition_timestamp` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `timestamps`
--

INSERT INTO `timestamps` (`timestamp_id`, `user_id`, `basic_timestamp`, `skill_timestamp`, `inventory_timestamp`, `condition_timestamp`) VALUES
(0, 1, '10', '10', '10', '10');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `type_id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `types`
--

INSERT INTO `types` (`type_id`, `name`) VALUES
(0, 'Basic'),
(1, 'Health Ingredient'),
(2, 'Mana Ingredient'),
(3, 'Time-Influence Ingredient'),
(4, 'Strentgh Ingredient'),
(5, 'Dungeoneering Ingredient'),
(6, 'Condenser Ingredient'),
(7, 'Basic Ingredient'),
(8, 'Mighty Ingredient - weak'),
(9, 'Mighty Ingredient - medium'),
(10, 'Mighty Ingredient - Strong'),
(11, 'Misc. ingredient'),
(12, 'Elfen Armor'),
(13, 'Herbalismn Potion'),
(14, 'Poisonous Potion'),
(15, 'Unbrewable Potion'),
(16, 'Elven Armor'),
(17, 'Human Armor'),
(18, 'Dwarfen Armor'),
(19, 'Dragonborn Armor'),
(20, 'Halfling Armor'),
(21, 'Cleric Armor'),
(22, 'Mage Armor'),
(23, 'Warrior Armor'),
(24, 'Palladin Armor'),
(25, 'Rogue Armor'),
(26, 'Level Armor'),
(27, 'Special Armor'),
(28, 'KaartObject (Zie kaart voor verdere Info)');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`user_id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permission_type` int(11) NOT NULL,
  `race` int(11) NOT NULL COMMENT 'The race of the Character',
  `class` int(11) NOT NULL COMMENT 'The class of the Character',
  `general_timestamp` int(11) NOT NULL COMMENT 'time since last update'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `permission_type`, `race`, `class`, `general_timestamp`) VALUES
(1, 'Wheatley', '$2a$10$t1pFRJNqG33OUKhCyDj3fuph.w5KTVypm5/3jrOKJ0HmiB1M4f0uy', 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user_basic_data`
--

CREATE TABLE IF NOT EXISTS `user_basic_data` (
  `ubd_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `basic_id` int(11) NOT NULL,
  `basic_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `user_basic_data`
--

INSERT INTO `user_basic_data` (`ubd_id`, `user_id`, `basic_id`, `basic_value`) VALUES
(0, 1, 1, 1),
(1, 1, 2, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user_condition_data`
--

CREATE TABLE IF NOT EXISTS `user_condition_data` (
  `ucd_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `condition_id` int(11) NOT NULL,
  `condition_value` int(11) NOT NULL COMMENT 'Dit bevat hoeveel beurten de conditie nog actief is'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `user_condition_data`
--

INSERT INTO `user_condition_data` (`ucd_id`, `user_id`, `condition_id`, `condition_value`) VALUES
(0, 1, 1, 1),
(1, 1, 2, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user_inventory_data`
--

CREATE TABLE IF NOT EXISTS `user_inventory_data` (
  `uid_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `user_inventory_data`
--

INSERT INTO `user_inventory_data` (`uid_id`, `user_id`, `item_id`, `item_value`) VALUES
(0, 1, 1, 1),
(1, 1, 2, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user_skill_data`
--

CREATE TABLE IF NOT EXISTS `user_skill_data` (
  `usd_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `skill_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `user_skill_data`
--

INSERT INTO `user_skill_data` (`usd_id`, `user_id`, `skill_id`, `skill_value`) VALUES
(0, 1, 1, 1),
(1, 1, 2, 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `advantages`
--
ALTER TABLE `advantages`
 ADD PRIMARY KEY (`advantage_id`);

--
-- Indexen voor tabel `basic`
--
ALTER TABLE `basic`
 ADD PRIMARY KEY (`basic_id`);

--
-- Indexen voor tabel `condition`
--
ALTER TABLE `condition`
 ADD PRIMARY KEY (`condition_id`);

--
-- Indexen voor tabel `inventory`
--
ALTER TABLE `inventory`
 ADD PRIMARY KEY (`item_id`);

--
-- Indexen voor tabel `monsters`
--
ALTER TABLE `monsters`
 ADD PRIMARY KEY (`monster_id`);

--
-- Indexen voor tabel `permission`
--
ALTER TABLE `permission`
 ADD PRIMARY KEY (`permission_id`);

--
-- Indexen voor tabel `races`
--
ALTER TABLE `races`
 ADD PRIMARY KEY (`race_id`);

--
-- Indexen voor tabel `shop`
--
ALTER TABLE `shop`
 ADD PRIMARY KEY (`item_id`);

--
-- Indexen voor tabel `skills`
--
ALTER TABLE `skills`
 ADD PRIMARY KEY (`skill_id`);

--
-- Indexen voor tabel `timestamps`
--
ALTER TABLE `timestamps`
 ADD PRIMARY KEY (`timestamp_id`);

--
-- Indexen voor tabel `types`
--
ALTER TABLE `types`
 ADD PRIMARY KEY (`type_id`);

--
-- Indexen voor tabel `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`user_id`);

--
-- Indexen voor tabel `user_basic_data`
--
ALTER TABLE `user_basic_data`
 ADD PRIMARY KEY (`ubd_id`);

--
-- Indexen voor tabel `user_condition_data`
--
ALTER TABLE `user_condition_data`
 ADD PRIMARY KEY (`ucd_id`);

--
-- Indexen voor tabel `user_inventory_data`
--
ALTER TABLE `user_inventory_data`
 ADD PRIMARY KEY (`uid_id`);

--
-- Indexen voor tabel `user_skill_data`
--
ALTER TABLE `user_skill_data`
 ADD PRIMARY KEY (`usd_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `permission`
--
ALTER TABLE `permission`
MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT voor een tabel `user`
--
ALTER TABLE `user`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
