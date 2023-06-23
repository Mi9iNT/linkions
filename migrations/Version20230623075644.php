<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230623075644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consent (id INT AUTO_INCREMENT NOT NULL, user_consent_id INT DEFAULT NULL, consent_value VARCHAR(3) DEFAULT NULL, UNIQUE INDEX UNIQ_63120810E6605B8F (user_consent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consent ADD CONSTRAINT FK_63120810E6605B8F FOREIGN KEY (user_consent_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consent DROP FOREIGN KEY FK_63120810E6605B8F');
        $this->addSql('DROP TABLE consent');
    }
}
