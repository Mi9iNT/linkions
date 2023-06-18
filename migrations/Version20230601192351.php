<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601192351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFA76ED395');
        $this->addSql('DROP INDEX IDX_404021BFA76ED395 ON formation');
        $this->addSql('ALTER TABLE formation CHANGE formation_validee formation_validee VARCHAR(255) DEFAULT NULL, CHANGE user_id formation_user_id INT DEFAULT NULL, CHANGE formation_name formation_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFE29196EE FOREIGN KEY (formation_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_404021BFE29196EE ON formation (formation_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFE29196EE');
        $this->addSql('DROP INDEX IDX_404021BFE29196EE ON formation');
        $this->addSql('ALTER TABLE formation CHANGE formation_validee formation_validee VARCHAR(3) DEFAULT NULL, CHANGE formation_user_id user_id INT DEFAULT NULL, CHANGE formation_title formation_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_404021BFA76ED395 ON formation (user_id)');
    }
}
