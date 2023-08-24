<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824195040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice CHANGE vat vat INT DEFAULT 3.33 NOT NULL');
        $this->addSql('ALTER TABLE storage CHANGE initial_capacity initial_capacity BIGINT DEFAULT 2000 NOT NULL, CHANGE left_capacity left_capacity BIGINT DEFAULT 2000 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage CHANGE initial_capacity initial_capacity INT DEFAULT 2 NOT NULL, CHANGE left_capacity left_capacity INT DEFAULT 2 NOT NULL');
        $this->addSql('ALTER TABLE invoice CHANGE vat vat INT DEFAULT 3 NOT NULL');
    }
}
