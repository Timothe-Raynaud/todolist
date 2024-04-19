<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419082748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
//         this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL DEFAULT (\'["ROLE_USER"]\')');
        $this->addSql("INSERT INTO user(username, password, email, roles) VALUES ('Anonyme', 'NotNeeded', 'anonyme@anonyme.com', '[\"ROLE_USER\"]')");
        $this->addSql('ALTER TABLE task ADD user_id INT');
        $this->addSql("UPDATE task SET user_id = (SELECT id FROM user WHERE username = 'Anonyme') WHERE 1");
        $this->addSql('ALTER TABLE task MODIFY user_id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_527EDB25A76ED395 ON task (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25A76ED395');
        $this->addSql('DROP INDEX IDX_527EDB25A76ED395 ON task');
        $this->addSql('ALTER TABLE task DROP user_id');
        $this->addSql('ALTER TABLE user DROP roles');
    }
}
