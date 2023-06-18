<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230603195605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE visibility_profil (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, user_visbility VARCHAR(255) DEFAULT NULL, admin_visibility VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_EB4E4BE2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE visibility_profil ADD CONSTRAINT FK_EB4E4BE2A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE formation CHANGE formation_date_debut formation_date_debut DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE formation_date_fin formation_date_fin DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visibility_profil DROP FOREIGN KEY FK_EB4E4BE2A76ED395');
        $this->addSql('DROP TABLE visibility_profil');
        $this->addSql('ALTER TABLE formation CHANGE formation_date_debut formation_date_debut DATE DEFAULT NULL, CHANGE formation_date_fin formation_date_fin DATE DEFAULT NULL');
    }
}
