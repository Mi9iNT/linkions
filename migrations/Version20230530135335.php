<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230530135335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FA7799308');
        $this->addSql('DROP INDEX UNIQ_1677722FA7799308 ON avatar');
        $this->addSql('ALTER TABLE avatar ADD avatar_name VARCHAR(255) DEFAULT NULL, CHANGE avatar_name_id user_avatar_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722F86D8B6F4 FOREIGN KEY (user_avatar_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1677722F86D8B6F4 ON avatar (user_avatar_id)');
        $this->addSql('ALTER TABLE curriculum_vitae DROP FOREIGN KEY FK_1FC99844A0F8C6C8');
        $this->addSql('DROP INDEX UNIQ_1FC99844A0F8C6C8 ON curriculum_vitae');
        $this->addSql('ALTER TABLE curriculum_vitae ADD curriculum_name VARCHAR(255) DEFAULT NULL, CHANGE curriculum_vitae_name_id curriculum_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE curriculum_vitae ADD CONSTRAINT FK_1FC99844708DF48B FOREIGN KEY (curriculum_user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1FC99844708DF48B ON curriculum_vitae (curriculum_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722F86D8B6F4');
        $this->addSql('DROP INDEX UNIQ_1677722F86D8B6F4 ON avatar');
        $this->addSql('ALTER TABLE avatar DROP avatar_name, CHANGE user_avatar_id avatar_name_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FA7799308 FOREIGN KEY (avatar_name_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1677722FA7799308 ON avatar (avatar_name_id)');
        $this->addSql('ALTER TABLE curriculum_vitae DROP FOREIGN KEY FK_1FC99844708DF48B');
        $this->addSql('DROP INDEX UNIQ_1FC99844708DF48B ON curriculum_vitae');
        $this->addSql('ALTER TABLE curriculum_vitae DROP curriculum_name, CHANGE curriculum_user_id curriculum_vitae_name_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE curriculum_vitae ADD CONSTRAINT FK_1FC99844A0F8C6C8 FOREIGN KEY (curriculum_vitae_name_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1FC99844A0F8C6C8 ON curriculum_vitae (curriculum_vitae_name_id)');
    }
}
