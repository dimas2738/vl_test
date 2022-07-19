<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220716123523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE process (id INT AUTO_INCREMENT NOT NULL, cpu_need INT NOT NULL, ram_need INT NOT NULL, id_machine INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE machine CHANGE cpu_remaind cpu_remaind INT NOT NULL, CHANGE ram_remaind ram_remaind INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE process');
        $this->addSql('ALTER TABLE machine CHANGE cpu_remaind cpu_remaind INT DEFAULT NULL, CHANGE ram_remaind ram_remaind INT DEFAULT NULL');
    }
}
