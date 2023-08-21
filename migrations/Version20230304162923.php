<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230304162923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, storage_id INT NOT NULL, name VARCHAR(255) NOT NULL, format VARCHAR(45) NOT NULL, size INT NOT NULL, upload_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_63540595CC5DB90 (storage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, invoice_number VARCHAR(45) NOT NULL, price NUMERIC(5, 2) DEFAULT \'16.67\' NOT NULL, vat INT DEFAULT 3.33 NOT NULL, incl_taxe INT DEFAULT 20 NOT NULL, create_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_90651744A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, initial_capacity BIGINT NOT NULL, left_capacity BIGINT NOT NULL, UNIQUE INDEX UNIQ_547A1B34A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(45) NOT NULL, lastname VARCHAR(45) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(32) NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(45) NOT NULL, super_admin TINYINT(1) NOT NULL, create_datetime DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_63540595CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_63540595CC5DB90');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A76ED395');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34A76ED395');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE storage');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
