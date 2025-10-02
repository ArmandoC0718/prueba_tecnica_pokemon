<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251001020111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE move (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_EF3E37785E237E06 (name), INDEX IDX_EF3E3778C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pokemon (id INT AUTO_INCREMENT NOT NULL, trainer_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, nickname VARCHAR(100) NOT NULL, level INT NOT NULL, health_points INT NOT NULL, attack INT NOT NULL, defense INT NOT NULL, speed INT NOT NULL, catch_rate INT NOT NULL, INDEX IDX_62DC90F3FB08EDF6 (trainer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pokemon_type (pokemon_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_B077296A2FE71C3E (pokemon_id), INDEX IDX_B077296AC54C8C93 (type_id), PRIMARY KEY(pokemon_id, type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pokemon_move (pokemon_id INT NOT NULL, move_id INT NOT NULL, INDEX IDX_D397493B2FE71C3E (pokemon_id), INDEX IDX_D397493B6DC541A8 (move_id), PRIMARY KEY(pokemon_id, move_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_8CDE57295E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE move ADD CONSTRAINT FK_EF3E3778C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE pokemon ADD CONSTRAINT FK_62DC90F3FB08EDF6 FOREIGN KEY (trainer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pokemon_type ADD CONSTRAINT FK_B077296A2FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id)');
        $this->addSql('ALTER TABLE pokemon_type ADD CONSTRAINT FK_B077296AC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE pokemon_move ADD CONSTRAINT FK_D397493B2FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id)');
        $this->addSql('ALTER TABLE pokemon_move ADD CONSTRAINT FK_D397493B6DC541A8 FOREIGN KEY (move_id) REFERENCES move (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE move DROP FOREIGN KEY FK_EF3E3778C54C8C93');
        $this->addSql('ALTER TABLE pokemon DROP FOREIGN KEY FK_62DC90F3FB08EDF6');
        $this->addSql('ALTER TABLE pokemon_type DROP FOREIGN KEY FK_B077296A2FE71C3E');
        $this->addSql('ALTER TABLE pokemon_type DROP FOREIGN KEY FK_B077296AC54C8C93');
        $this->addSql('ALTER TABLE pokemon_move DROP FOREIGN KEY FK_D397493B2FE71C3E');
        $this->addSql('ALTER TABLE pokemon_move DROP FOREIGN KEY FK_D397493B6DC541A8');
        $this->addSql('DROP TABLE move');
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('DROP TABLE pokemon_type');
        $this->addSql('DROP TABLE pokemon_move');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE user');
    }
}
