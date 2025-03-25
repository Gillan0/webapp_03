<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324093430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item RENAME INDEX idx_1f1b251ee141bcea TO IDX_1F1B251EFB8E54CD');
        $this->addSql('ALTER TABLE purchased_item ADD CONSTRAINT FK_F84821416C755722 FOREIGN KEY (buyer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchased_item ADD CONSTRAINT FK_F8482141BF396750 FOREIGN KEY (id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64918F45C82 FOREIGN KEY (website_id) REFERENCES website (id)');
        $this->addSql('ALTER TABLE user_contributing_wishlists ADD CONSTRAINT FK_9F3DD295A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_contributing_wishlists ADD CONSTRAINT FK_9F3DD295FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_invited_wishlists ADD CONSTRAINT FK_F2A6939EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_invited_wishlists ADD CONSTRAINT FK_F2A6939EFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist CHANGE deadline deadline DATETIME NOT NULL');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_contributing_wishlists DROP FOREIGN KEY FK_9F3DD295A76ED395');
        $this->addSql('ALTER TABLE user_contributing_wishlists DROP FOREIGN KEY FK_9F3DD295FB8E54CD');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64918F45C82');
        $this->addSql('ALTER TABLE purchased_item DROP FOREIGN KEY FK_F84821416C755722');
        $this->addSql('ALTER TABLE purchased_item DROP FOREIGN KEY FK_F8482141BF396750');
        $this->addSql('ALTER TABLE user_invited_wishlists DROP FOREIGN KEY FK_F2A6939EA76ED395');
        $this->addSql('ALTER TABLE user_invited_wishlists DROP FOREIGN KEY FK_F2A6939EFB8E54CD');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31F675F31B');
        $this->addSql('ALTER TABLE wishlist CHANGE deadline deadline DATE NOT NULL');
        $this->addSql('ALTER TABLE item RENAME INDEX idx_1f1b251efb8e54cd TO IDX_1F1B251EE141BCEA');
    }
}
