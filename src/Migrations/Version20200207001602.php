<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200207001602 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE discipline ADD COLUMN d_streamed BOOLEAN DEFAULT false');
        $this->addSql('ALTER TABLE user ADD COLUMN avatar VARCHAR(255) DEFAULT \'https://cdn.discordapp.com/avatars/239740613581471744/fd0233fe3bac0217486ce1f8210b9ee4.webp?size=2048\'');
        $this->addSql('ALTER TABLE user ADD COLUMN tag VARCHAR(255) DEFAULT \'undefined\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__discipline AS SELECT id, d_game, d_format, d_organisateur, d_start_date, d_pts_winner, d_pts_loser, d_default, d_coeff, d_banner, d_finished, d_description FROM discipline');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('CREATE TABLE discipline (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, d_game VARCHAR(255) NOT NULL, d_format VARCHAR(255) NOT NULL, d_organisateur VARCHAR(255) NOT NULL, d_start_date INTEGER DEFAULT NULL, d_pts_winner INTEGER DEFAULT NULL, d_pts_loser INTEGER DEFAULT NULL, d_default INTEGER DEFAULT NULL, d_coeff DOUBLE PRECISION DEFAULT NULL, d_banner VARCHAR(255) NOT NULL, d_finished INTEGER NOT NULL, d_description CLOB NOT NULL)');
        $this->addSql('INSERT INTO discipline (id, d_game, d_format, d_organisateur, d_start_date, d_pts_winner, d_pts_loser, d_default, d_coeff, d_banner, d_finished, d_description) SELECT id, d_game, d_format, d_organisateur, d_start_date, d_pts_winner, d_pts_loser, d_default, d_coeff, d_banner, d_finished, d_description FROM __temp__discipline');
        $this->addSql('DROP TABLE __temp__discipline');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, role FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, username, password, role) SELECT id, username, password, role FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }
}
