<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230831182026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file CHANGE size size NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36105CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE file RENAME INDEX idx_63540595cc5db90 TO IDX_8C9F36105CC5DB90');
        $this->addSql('ALTER TABLE invoice CHANGE vat vat DOUBLE PRECISION DEFAULT \'3.33\' NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE storage CHANGE initial_capacity initial_capacity BIGINT DEFAULT 20000000000 NOT NULL, CHANGE left_capacity left_capacity BIGINT DEFAULT 20000000000 NOT NULL');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD payments_count INT DEFAULT 0 NOT NULL, CHANGE email email VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36105CC5DB90');
        $this->addSql('ALTER TABLE file CHANGE size size INT NOT NULL');
        $this->addSql('ALTER TABLE file RENAME INDEX idx_8c9f36105cc5db90 TO IDX_63540595CC5DB90');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A76ED395');
        $this->addSql('ALTER TABLE invoice CHANGE vat vat INT DEFAULT 3 NOT NULL');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34A76ED395');
        $this->addSql('ALTER TABLE storage CHANGE initial_capacity initial_capacity BIGINT NOT NULL, CHANGE left_capacity left_capacity BIGINT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP payments_count, CHANGE email email VARCHAR(255) NOT NULL');
    }
}
