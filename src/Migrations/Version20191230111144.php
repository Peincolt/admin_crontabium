<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191230111144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE squad (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE squad_hero (squad_id INT NOT NULL, hero_id INT NOT NULL, INDEX IDX_89F1FD12DF1B2C7C (squad_id), INDEX IDX_89F1FD1245B0BCD (hero_id), PRIMARY KEY(squad_id, hero_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE squad_ship (squad_id INT NOT NULL, ship_id INT NOT NULL, INDEX IDX_220F78B0DF1B2C7C (squad_id), INDEX IDX_220F78B0C256317D (ship_id), PRIMARY KEY(squad_id, ship_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE squad_hero ADD CONSTRAINT FK_89F1FD12DF1B2C7C FOREIGN KEY (squad_id) REFERENCES squad (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE squad_hero ADD CONSTRAINT FK_89F1FD1245B0BCD FOREIGN KEY (hero_id) REFERENCES hero (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE squad_ship ADD CONSTRAINT FK_220F78B0DF1B2C7C FOREIGN KEY (squad_id) REFERENCES squad (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE squad_ship ADD CONSTRAINT FK_220F78B0C256317D FOREIGN KEY (ship_id) REFERENCES ship (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE squad_hero DROP FOREIGN KEY FK_89F1FD12DF1B2C7C');
        $this->addSql('ALTER TABLE squad_ship DROP FOREIGN KEY FK_220F78B0DF1B2C7C');
        $this->addSql('DROP TABLE squad');
        $this->addSql('DROP TABLE squad_hero');
        $this->addSql('DROP TABLE squad_ship');
    }
}
