<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226210959 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, upload_at DATETIME NOT NULL, view_at DATETIME DEFAULT NULL, views INT DEFAULT NULL, image_name VARCHAR(255) NOT NULL, image_size INT NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_C53D045F12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_tag (image_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_5B6367D03DA5256D (image_id), INDEX IDX_5B6367D0BAD26311 (tag_id), PRIMARY KEY(image_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, days_before_delete INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE image_tag ADD CONSTRAINT FK_5B6367D03DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_tag ADD CONSTRAINT FK_5B6367D0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE image_tag DROP FOREIGN KEY FK_5B6367D03DA5256D');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F12469DE2');
        $this->addSql('ALTER TABLE image_tag DROP FOREIGN KEY FK_5B6367D0BAD26311');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE image_tag');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE tag');
    }
}
