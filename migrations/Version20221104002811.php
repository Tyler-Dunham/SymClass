<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221104002811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE syllabus DROP FOREIGN KEY FK_4E74AB926278D5A8');
        $this->addSql('DROP TABLE syllabus');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE syllabus (id INT AUTO_INCREMENT NOT NULL, classroom_id INT NOT NULL, UNIQUE INDEX UNIQ_4E74AB926278D5A8 (classroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE syllabus ADD CONSTRAINT FK_4E74AB926278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id)');
    }
}
