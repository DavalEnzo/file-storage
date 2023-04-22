<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319165059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice CHANGE vat vat INT DEFAULT 3.33 NOT NULL');
        $this->addSql('ALTER TABLE user DROP super_admin');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD super_admin TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE invoice CHANGE vat vat INT DEFAULT 3 NOT NULL');
    }
}
