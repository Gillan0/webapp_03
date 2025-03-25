<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325181807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchased_item DROP FOREIGN KEY FK_F84821416C755722');
        $this->addSql('DROP INDEX IDX_F84821416C755722 ON purchased_item');
        $this->addSql('ALTER TABLE purchased_item ADD buyer VARCHAR(20) NOT NULL, DROP buyer_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchased_item ADD buyer_id INT NOT NULL, DROP buyer');
        $this->addSql('ALTER TABLE purchased_item ADD CONSTRAINT FK_F84821416C755722 FOREIGN KEY (buyer_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F84821416C755722 ON purchased_item (buyer_id)');
    }
}
