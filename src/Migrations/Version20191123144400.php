<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191123144400 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Moderation RENAME INDEX idx_8fb76974700047d2 TO IDX_C0EA6AA4700047D2');
        $this->addSql('ALTER TABLE Moderation RENAME INDEX idx_8fb76974a76ed395 TO IDX_C0EA6AA4A76ED395');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE moderation RENAME INDEX idx_c0ea6aa4700047d2 TO IDX_8FB76974700047D2');
        $this->addSql('ALTER TABLE moderation RENAME INDEX idx_c0ea6aa4a76ed395 TO IDX_8FB76974A76ED395');
    }
}
