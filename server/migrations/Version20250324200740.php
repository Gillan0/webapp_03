<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324200740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CE12A31F4818D99 ON wishlist (sharing_url)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CE12A312023EEF4 ON wishlist (display_url)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_9CE12A31F4818D99 ON wishlist');
        $this->addSql('DROP INDEX UNIQ_9CE12A312023EEF4 ON wishlist');
    }
}
