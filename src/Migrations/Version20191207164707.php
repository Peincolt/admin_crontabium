<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191207164707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE hero (id INT AUTO_INCREMENT NOT NULL, base_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hero_player (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, hero_id INT DEFAULT NULL, number_stars SMALLINT NOT NULL, level SMALLINT NOT NULL, gear_level SMALLINT NOT NULL, galactical_puissance VARCHAR(255) NOT NULL, relic_level SMALLINT NOT NULL, INDEX IDX_9D86127999E6F5DF (player_id), INDEX IDX_9D86127945B0BCD (hero_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, last_updated DATETIME NOT NULL, ally_code INT NOT NULL, name VARCHAR(255) NOT NULL, level SMALLINT NOT NULL, galactical_puissance VARCHAR(255) NOT NULL, characters_galactical_puissance VARCHAR(255) NOT NULL, ships_galactical_puissance VARCHAR(255) NOT NULL, gear_given INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ship (id INT AUTO_INCREMENT NOT NULL, base_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ship_player (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, ship_id INT DEFAULT NULL, number_stars VARCHAR(255) NOT NULL, level SMALLINT NOT NULL, galactical_puissance VARCHAR(255) NOT NULL, INDEX IDX_6EA1256D99E6F5DF (player_id), INDEX IDX_6EA1256DC256317D (ship_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hero_player ADD CONSTRAINT FK_9D86127999E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE hero_player ADD CONSTRAINT FK_9D86127945B0BCD FOREIGN KEY (hero_id) REFERENCES hero (id)');
        $this->addSql('ALTER TABLE ship_player ADD CONSTRAINT FK_6EA1256D99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE ship_player ADD CONSTRAINT FK_6EA1256DC256317D FOREIGN KEY (ship_id) REFERENCES ship (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hero_player DROP FOREIGN KEY FK_9D86127945B0BCD');
        $this->addSql('ALTER TABLE hero_player DROP FOREIGN KEY FK_9D86127999E6F5DF');
        $this->addSql('ALTER TABLE ship_player DROP FOREIGN KEY FK_6EA1256D99E6F5DF');
        $this->addSql('ALTER TABLE ship_player DROP FOREIGN KEY FK_6EA1256DC256317D');
        $this->addSql('DROP TABLE hero');
        $this->addSql('DROP TABLE hero_player');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE ship');
        $this->addSql('DROP TABLE ship_player');
    }
}
