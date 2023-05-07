<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230504102426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blague (id INT AUTO_INCREMENT NOT NULL, humouriste_id INT DEFAULT NULL, libelle VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, nom_meme VARCHAR(255) DEFAULT NULL, INDEX IDX_9AEC0192B206A5 (humouriste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE humouriste (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, actif TINYINT(1) DEFAULT 1 NOT NULL, `admin` TINYINT(1) DEFAULT 0 NOT NULL, nom_image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_13714B76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blague ADD CONSTRAINT FK_9AEC0192B206A5 FOREIGN KEY (humouriste_id) REFERENCES humouriste (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blague DROP FOREIGN KEY FK_9AEC0192B206A5');
        $this->addSql('DROP TABLE blague');
        $this->addSql('DROP TABLE humouriste');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
