-- =====================================================
-- BudgetMaster - Schéma de la base de données
-- MySQL 5.7+ / MariaDB 10.3+
-- =====================================================

CREATE DATABASE IF NOT EXISTS budgetmaster
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE budgetmaster;

SET NAMES utf8mb4;

-- -----------------------------------------------------
-- Utilisateurs
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    country VARCHAR(100) DEFAULT 'France',
    language VARCHAR(30) DEFAULT 'Français',
    currency VARCHAR(10) DEFAULT 'EUR',
    theme ENUM('rose','bleu','violet') DEFAULT 'rose',
    user_type ENUM('Etudiant','Salarié','Freelance','Famille') DEFAULT NULL,
    monthly_income DECIMAL(12,2) DEFAULT 0.00,
    picture VARCHAR(255) DEFAULT 'default.png',
    email_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Paramètres utilisateur (langue, devise, thème)
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS settings (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    language VARCHAR(30) DEFAULT NULL,
    currency VARCHAR(10) DEFAULT NULL,
    theme VARCHAR(20) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY user_id (user_id),
    CONSTRAINT settings_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Comptes bancaires (prévu dans le cahier des charges)
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS accounts (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) DEFAULT NULL,
    type ENUM('Courant','Epargne','PEA','Livret','Crypto','Espèces') DEFAULT NULL,
    balance DECIMAL(12,2) DEFAULT 0.00,
    color VARCHAR(20) DEFAULT '#FF5FA2',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    CONSTRAINT accounts_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Catégories
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) DEFAULT NULL,
    type ENUM('income','expense') DEFAULT NULL,
    icon VARCHAR(100) DEFAULT NULL,
    color VARCHAR(20) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    CONSTRAINT categories_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Transactions (revenus et dépenses)
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS transactions (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('income','expense') NOT NULL,
    category VARCHAR(50) NOT NULL,
    transaction_date DATE NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_date (user_id, transaction_date),
    CONSTRAINT transactions_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Budgets mensuels
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS budgets (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    month INT DEFAULT NULL,
    year INT DEFAULT NULL,
    amount DECIMAL(12,2) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    CONSTRAINT budgets_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Objectifs financiers
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS goals (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    title VARCHAR(200) DEFAULT NULL,
    target_amount DECIMAL(12,2) DEFAULT NULL,
    current_amount DECIMAL(12,2) DEFAULT 0.00,
    deadline DATE DEFAULT NULL,
    status ENUM('En cours','Terminé') DEFAULT 'En cours',
    PRIMARY KEY (id),
    KEY user_id (user_id),
    CONSTRAINT goals_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Épargne (versements, éventuellement liés à un objectif)
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS savings (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    goal_id INT DEFAULT NULL,
    title VARCHAR(200) DEFAULT NULL,
    amount DECIMAL(12,2) DEFAULT NULL,
    saving_date DATE DEFAULT NULL,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    KEY fk_goal (goal_id),
    CONSTRAINT fk_goal FOREIGN KEY (goal_id) REFERENCES goals (id) ON DELETE CASCADE,
    CONSTRAINT savings_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Investissements
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS investments (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    name VARCHAR(200) DEFAULT NULL,
    platform VARCHAR(100) DEFAULT NULL,
    invested DECIMAL(12,2) DEFAULT NULL,
    current_value DECIMAL(12,2) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    CONSTRAINT investments_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Notifications
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS notifications (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    message TEXT DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    CONSTRAINT notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
