<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114104150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus CHANGE nom nom VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D0968116C6E55B5 ON campus (nom)');
        $this->addSql('ALTER TABLE etat CHANGE libelle libelle VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55CAF762A4D60759 ON etat (libelle)');
        $this->addSql('ALTER TABLE participant DROP roles, CHANGE pseudo pseudo VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79F6B1186CC499D ON participant (pseudo)');
        $this->addSql('ALTER TABLE sortie CHANGE duree duree INT NOT NULL, CHANGE date_limite_inscription date_limite_inscription DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_9D0968116C6E55B5 ON campus');
        $this->addSql('ALTER TABLE campus CHANGE nom nom VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_D79F6B1186CC499D ON participant');
        $this->addSql('ALTER TABLE participant ADD roles JSON NOT NULL, CHANGE pseudo pseudo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sortie CHANGE duree duree VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', CHANGE date_limite_inscription date_limite_inscription DATETIME NOT NULL');
        $this->addSql('DROP INDEX UNIQ_55CAF762A4D60759 ON etat');
        $this->addSql('ALTER TABLE etat CHANGE libelle libelle VARCHAR(255) NOT NULL');
    }
}
