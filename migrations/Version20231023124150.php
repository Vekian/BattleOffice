<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023124150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address ADD firstname VARCHAR(1000) NOT NULL, ADD lastname VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE client DROP firstname, DROP lastname');
        $this->addSql('ALTER TABLE `order` RENAME INDEX idx_f5299398115c78d4 TO IDX_F5299398439FD419');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` RENAME INDEX idx_f5299398439fd419 TO IDX_F5299398115C78D4');
        $this->addSql('ALTER TABLE address DROP firstname, DROP lastname');
        $this->addSql('ALTER TABLE client ADD firstname VARCHAR(1000) NOT NULL, ADD lastname VARCHAR(1000) NOT NULL');
    }
}
