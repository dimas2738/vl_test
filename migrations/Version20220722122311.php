<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220722122311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE process ADD machine_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D1896F6B75B26 FOREIGN KEY (machine_id) REFERENCES machine (id)');
        $this->addSql('CREATE INDEX IDX_861D1896F6B75B26 ON process (machine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D1896F6B75B26');
        $this->addSql('DROP INDEX IDX_861D1896F6B75B26 ON process');
        $this->addSql('ALTER TABLE process DROP machine_id');
    }
}
