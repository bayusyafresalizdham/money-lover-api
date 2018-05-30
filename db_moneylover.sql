-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2018 at 04:20 PM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_moneylover`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id_category` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_type` int(1) NOT NULL,
  `category_img` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id_category`, `category_name`, `category_type`, `category_img`) VALUES
(1, 'Belanja', 0, 'img1.png'),
(2, 'Gaji', 1, 'img1.png'),
(3, 'Pinjaman', 2, 'img1.png'),
(4, 'Bills & Utilities', 0, 'icon_135.png'),
(5, 'Education', 0, 'ic_category_education.png'),
(6, 'Entertainment', 0, 'ic_category_entertainment.png'),
(7, 'Family', 0, 'ic_category_family.png'),
(8, 'Fees & Charges', 0, 'ic_category_other_expense.png'),
(9, 'Food & Beverage', 0, 'ic_category_foodndrink.png'),
(10, 'Friends & Lover', 0, 'ic_category_friendnlover.png'),
(11, 'Gifts & Donations', 0, 'ic_category_give.png'),
(12, 'Health & Fitness', 0, 'ic_category_medical.png'),
(13, 'Insurances', 0, 'ic_category_other_expense.png'),
(14, 'Investment', 0, 'ic_category_invest.png'),
(15, 'Others', 0, 'ic_category_other_expense.png'),
(16, 'Shopping', 0, 'ic_category_shopping.png'),
(17, 'Transfer', 0, 'icon_142.png'),
(18, 'Transportation', 0, 'ic_category_transport.png'),
(19, 'Travel', 0, 'ic_category_travel.png'),
(20, 'Award', 1, 'ic_category_award.png'),
(21, 'Gifts', 1, 'ic_category_give.png'),
(22, 'Interest Money', 1, 'ic_category_interestmoney.png'),
(23, 'Others', 1, 'ic_category_other_expense.png'),
(24, 'Salary', 1, 'ic_category_salary.png'),
(25, 'Selling', 1, 'ic_category_selling.png'),
(26, 'Transfer', 1, 'icon_143.png'),
(27, 'Debt', 2, 'ic_category_debt.png'),
(28, 'Loan', 2, 'ic_category_loan.png');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id_transaction` int(11) NOT NULL,
  `nominal` int(20) NOT NULL,
  `note` varchar(100) NOT NULL,
  `with_name` varchar(100) NOT NULL,
  `id_wallet_user` int(11) NOT NULL,
  `date` date NOT NULL,
  `id_category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id_transaction`, `nominal`, `note`, `with_name`, `id_wallet_user`, `date`, `id_category`) VALUES
(1, 2000, 'belanja donut', 'andre', 1, '2018-05-10', 1),
(5, 20000, 'komedi1', 'andre', 3, '2018-05-10', 1),
(6, 200000, 'komedi1', 'andre', 2, '2018-05-10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `email`, `password`, `token`) VALUES
(1, 'bayusyafresalizdham@gmail.com', '9d5efc8d883087a4d84952411f8c2b5c', '5ca8c2eebc21839c556e93565afff42d'),
(12, 'bayu@gmail.com', '9d5efc8d883087a4d84952411f8c2b5c', '6d0f118a787005f93556c0233c5a90ac');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_type`
--

CREATE TABLE `wallet_type` (
  `id_wallet_type` int(11) NOT NULL,
  `wallet_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wallet_type`
--

INSERT INTO `wallet_type` (`id_wallet_type`, `wallet_name`) VALUES
(1, 'Rekening Bank'),
(2, 'Uang Tunai');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_user`
--

CREATE TABLE `wallet_user` (
  `id_wallet_user` int(11) NOT NULL,
  `id_wallet_type` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `name_from_user` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wallet_user`
--

INSERT INTO `wallet_user` (`id_wallet_user`, `id_wallet_type`, `id_user`, `name_from_user`) VALUES
(1, 1, 1, ''),
(2, 2, 1, ''),
(5, 1, 12, ''),
(6, 2, 12, ''),
(7, 3, 1, 'paypal');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id_category`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id_transaction`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `wallet_type`
--
ALTER TABLE `wallet_type`
  ADD PRIMARY KEY (`id_wallet_type`);

--
-- Indexes for table `wallet_user`
--
ALTER TABLE `wallet_user`
  ADD PRIMARY KEY (`id_wallet_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `wallet_type`
--
ALTER TABLE `wallet_type`
  MODIFY `id_wallet_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `wallet_user`
--
ALTER TABLE `wallet_user`
  MODIFY `id_wallet_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
