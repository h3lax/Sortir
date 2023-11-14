<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114162252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE sortie ADD mot_clef VARCHAR(255) DEFAULT NULL, CHANGE duree duree VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP roles');
        $this->addSql('ALTER TABLE sortie DROP mot_clef, CHANGE duree duree VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
    }
}
