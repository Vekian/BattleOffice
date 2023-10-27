<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231026114140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398115C78D4');
        $this->addSql('DROP INDEX IDX_F5299398439FD419 ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE adress_billing_id address_billing_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398439FD419 FOREIGN KEY (address_billing_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_F5299398439FD419 ON `order` (address_billing_id)');
        $this->addSql('ALTER TABLE product ADD price DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398439FD419');
        $this->addSql('DROP INDEX IDX_F5299398439FD419 ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE id id VARCHAR(255) NOT NULL, CHANGE address_billing_id adress_billing_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398115C78D4 FOREIGN KEY (adress_billing_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_F5299398439FD419 ON `order` (adress_billing_id)');
        $this->addSql('ALTER TABLE product DROP price');
    }
}
