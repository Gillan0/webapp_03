<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325174412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchased_item (id INT NOT NULL, congratulory_message VARCHAR(500) NOT NULL, purchase_proof VARCHAR(200) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchased_item ADD CONSTRAINT FK_F8482141BF396750 FOREIGN KEY (id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item DROP congratulory_message, DROP purchase_proof, DROP buyer');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchased_item DROP FOREIGN KEY FK_F8482141BF396750');
        $this->addSql('DROP TABLE purchased_item');
        $this->addSql('ALTER TABLE item ADD congratulory_message VARCHAR(500) DEFAULT NULL, ADD purchase_proof VARCHAR(200) DEFAULT NULL, ADD buyer VARCHAR(20) DEFAULT NULL');
    }
}
