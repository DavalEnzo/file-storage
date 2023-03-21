<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320175847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice CHANGE vat vat INT DEFAULT 3.33 NOT NULL');
        $this->addSql('ALTER TABLE user ADD status INT DEFAULT 0 NOT NULL, DROP is_buyer, CHANGE create_datetime create_datetime DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice CHANGE vat vat INT DEFAULT 3 NOT NULL');
        $this->addSql('ALTER TABLE user ADD is_buyer TINYINT(1) DEFAULT 0 NOT NULL, DROP status, CHANGE create_datetime create_datetime DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
