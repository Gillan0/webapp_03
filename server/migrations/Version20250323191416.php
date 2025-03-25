<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250323191416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EE141BCEA');
        $this->addSql('DROP INDEX IDX_1F1B251EE141BCEA ON item');
        $this->addSql('ALTER TABLE item CHANGE wishlish_id wishlist_id INT NOT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251EFB8E54CD ON item (wishlist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EFB8E54CD');
        $this->addSql('DROP INDEX IDX_1F1B251EFB8E54CD ON item');
        $this->addSql('ALTER TABLE item CHANGE wishlist_id wishlish_id INT NOT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EE141BCEA FOREIGN KEY (wishlish_id) REFERENCES wishlist (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1F1B251EE141BCEA ON item (wishlish_id)');
    }
}
