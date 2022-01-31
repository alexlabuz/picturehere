<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220131205414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, profil_id INT DEFAULT NULL, message LONGTEXT NOT NULL, link_image VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_5A8A6C8D275ED078 (profil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(50) NOT NULL, date_inscription DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil_profil (profil_source INT NOT NULL, profil_target INT NOT NULL, INDEX IDX_97293BC52E75F621 (profil_source), INDEX IDX_97293BC53790A6AE (profil_target), PRIMARY KEY(profil_source, profil_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, profil_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649275ED078 (profil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE profil_profil ADD CONSTRAINT FK_97293BC52E75F621 FOREIGN KEY (profil_source) REFERENCES profil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profil_profil ADD CONSTRAINT FK_97293BC53790A6AE FOREIGN KEY (profil_target) REFERENCES profil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D275ED078');
        $this->addSql('ALTER TABLE profil_profil DROP FOREIGN KEY FK_97293BC52E75F621');
        $this->addSql('ALTER TABLE profil_profil DROP FOREIGN KEY FK_97293BC53790A6AE');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649275ED078');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE profil_profil');
        $this->addSql('DROP TABLE `user`');
    }
}
