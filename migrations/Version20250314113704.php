<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250314113704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE file DROP filename');
        $this->addSql('ALTER TABLE file DROP extension');
        $this->addSql('ALTER TABLE "user" ALTER is_verified SET DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE file ADD extension VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE file RENAME COLUMN type TO filename');
        $this->addSql('ALTER TABLE "user" ALTER is_verified DROP DEFAULT');
    }
}
