<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308232134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE image_id_seq CASCADE');
        $this->addSql('CREATE TABLE file (id SERIAL NOT NULL, created_by_id INT NOT NULL, edited_by_id INT DEFAULT NULL, snowtrick_id INT NOT NULL, filename VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, edited_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8C9F3610B03A8386 ON file (created_by_id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610DD7B2EBC ON file (edited_by_id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610492EB8F3 ON file (snowtrick_id)');
        $this->addSql('COMMENT ON COLUMN file.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN file.edited_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610DD7B2EBC FOREIGN KEY (edited_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610492EB8F3 FOREIGN KEY (snowtrick_id) REFERENCES snowtrick (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image DROP CONSTRAINT fk_c53d045fa2b28fe8');
        $this->addSql('ALTER TABLE image DROP CONSTRAINT fk_c53d045f492eb8f3');
        $this->addSql('DROP TABLE image');
        $this->addSql('ALTER TABLE snowtrick DROP video');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE image (id SERIAL NOT NULL, uploaded_by_id INT DEFAULT NULL, snowtrick_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, extension VARCHAR(7) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_c53d045f492eb8f3 ON image (snowtrick_id)');
        $this->addSql('CREATE INDEX idx_c53d045fa2b28fe8 ON image (uploaded_by_id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT fk_c53d045fa2b28fe8 FOREIGN KEY (uploaded_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT fk_c53d045f492eb8f3 FOREIGN KEY (snowtrick_id) REFERENCES snowtrick (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F3610B03A8386');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F3610DD7B2EBC');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F3610492EB8F3');
        $this->addSql('DROP TABLE file');
        $this->addSql('ALTER TABLE snowtrick ADD video VARCHAR(255) DEFAULT NULL');
    }
}
