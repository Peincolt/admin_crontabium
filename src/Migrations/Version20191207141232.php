<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191207141232 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE character_player (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, id_swgoh VARCHAR(255) NOT NULL, international_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, number_stars SMALLINT NOT NULL, level SMALLINT NOT NULL, gear_level SMALLINT NOT NULL, galactical_puissance VARCHAR(255) NOT NULL, relic_level SMALLINT NOT NULL, INDEX IDX_3B5BB9F699E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ship_player (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, id_swgoh VARCHAR(255) NOT NULL, international_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, number_stars VARCHAR(255) NOT NULL, level SMALLINT NOT NULL, galactical_puissance VARCHAR(255) NOT NULL, INDEX IDX_6EA1256D99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE character_player ADD CONSTRAINT FK_3B5BB9F699E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE ship_player ADD CONSTRAINT FK_6EA1256D99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE character_player');
        $this->addSql('DROP TABLE ship_player');
    }
}
