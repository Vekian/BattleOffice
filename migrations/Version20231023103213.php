<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023103213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F5B7AF75');
        $this->addSql('DROP INDEX IDX_F5299398F5B7AF75 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD address_shipping_id INT NOT NULL, CHANGE address_id adress_billing_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398115C78D4 FOREIGN KEY (adress_billing_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993985F48D033 FOREIGN KEY (address_shipping_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_F5299398115C78D4 ON `order` (adress_billing_id)');
        $this->addSql('CREATE INDEX IDX_F52993985F48D033 ON `order` (address_shipping_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398115C78D4');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993985F48D033');
        $this->addSql('DROP INDEX IDX_F5299398115C78D4 ON `order`');
        $this->addSql('DROP INDEX IDX_F52993985F48D033 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD address_id INT NOT NULL, DROP adress_billing_id, DROP address_shipping_id');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_F5299398F5B7AF75 ON `order` (address_id)');
    }
}
