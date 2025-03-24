<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324163601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, wishlist_id INT NOT NULL, title VARCHAR(20) NOT NULL, description VARCHAR(500) NOT NULL, price DOUBLE PRECISION NOT NULL, url VARCHAR(200) NOT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_1F1B251EFB8E54CD (wishlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchased_item (id INT NOT NULL, buyer_id INT NOT NULL, congratulory_message VARCHAR(500) NOT NULL, purchase_proof VARCHAR(200) NOT NULL, INDEX IDX_F84821416C755722 (buyer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, website_id INT NOT NULL, username VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(50) NOT NULL, is_locked TINYINT(1) NOT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_8D93D64918F45C82 (website_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_contributing_wishlists (user_id INT NOT NULL, wishlist_id INT NOT NULL, INDEX IDX_9F3DD295A76ED395 (user_id), INDEX IDX_9F3DD295FB8E54CD (wishlist_id), PRIMARY KEY(user_id, wishlist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_invited_wishlists (user_id INT NOT NULL, wishlist_id INT NOT NULL, INDEX IDX_F2A6939EA76ED395 (user_id), INDEX IDX_F2A6939EFB8E54CD (wishlist_id), PRIMARY KEY(user_id, wishlist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE website (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, deadline DATETIME NOT NULL, name VARCHAR(20) NOT NULL, sharing_url VARCHAR(100) NOT NULL, display_url VARCHAR(100) NOT NULL, INDEX IDX_9CE12A31F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)');
        $this->addSql('ALTER TABLE purchased_item ADD CONSTRAINT FK_F84821416C755722 FOREIGN KEY (buyer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchased_item ADD CONSTRAINT FK_F8482141BF396750 FOREIGN KEY (id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64918F45C82 FOREIGN KEY (website_id) REFERENCES website (id)');
        $this->addSql('ALTER TABLE user_contributing_wishlists ADD CONSTRAINT FK_9F3DD295A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_contributing_wishlists ADD CONSTRAINT FK_9F3DD295FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_invited_wishlists ADD CONSTRAINT FK_F2A6939EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_invited_wishlists ADD CONSTRAINT FK_F2A6939EFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EFB8E54CD');
        $this->addSql('ALTER TABLE purchased_item DROP FOREIGN KEY FK_F84821416C755722');
        $this->addSql('ALTER TABLE purchased_item DROP FOREIGN KEY FK_F8482141BF396750');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64918F45C82');
        $this->addSql('ALTER TABLE user_contributing_wishlists DROP FOREIGN KEY FK_9F3DD295A76ED395');
        $this->addSql('ALTER TABLE user_contributing_wishlists DROP FOREIGN KEY FK_9F3DD295FB8E54CD');
        $this->addSql('ALTER TABLE user_invited_wishlists DROP FOREIGN KEY FK_F2A6939EA76ED395');
        $this->addSql('ALTER TABLE user_invited_wishlists DROP FOREIGN KEY FK_F2A6939EFB8E54CD');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31F675F31B');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE purchased_item');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_contributing_wishlists');
        $this->addSql('DROP TABLE user_invited_wishlists');
        $this->addSql('DROP TABLE website');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
