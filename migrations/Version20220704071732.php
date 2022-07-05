<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704071732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings ADD end_time DATETIME NOT NULL, CHANGE car car VARCHAR(10) NOT NULL, CHANGE plug plug INT NOT NULL');
//        $this->addSql('ALTER TABLE plugs DROP FOREIGN KEY plugs_FK');
//        $this->addSql('ALTER TABLE plugs CHANGE station station CHAR(36) DEFAULT NULL');
//        $this->addSql('DROP INDEX plugs_fk ON plugs');
//        $this->addSql('CREATE INDEX Plugs_fk0 ON plugs (station)');
//        $this->addSql('ALTER TABLE plugs ADD CONSTRAINT plugs_FK FOREIGN KEY (station) REFERENCES stations (uuid)');
//        $this->addSql('ALTER TABLE stations CHANGE uuid uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user_car CHANGE user user INT NOT NULL, CHANGE car car VARCHAR(10) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings DROP end_time, CHANGE car car VARCHAR(10) NOT NULL, CHANGE plug plug INT NOT NULL');
//        $this->addSql('ALTER TABLE plugs DROP FOREIGN KEY FK_FF8A28349F39F8B1');
//        $this->addSql('ALTER TABLE plugs CHANGE station station CHAR(36) NOT NULL');
//        $this->addSql('DROP INDEX plugs_fk0 ON plugs');
//        $this->addSql('CREATE INDEX plugs_FK ON plugs (station)');
//        $this->addSql('ALTER TABLE plugs ADD CONSTRAINT FK_FF8A28349F39F8B1 FOREIGN KEY (station) REFERENCES stations (uuid)');
//        $this->addSql('ALTER TABLE stations CHANGE uuid uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_car CHANGE user user INT NOT NULL, CHANGE car car VARCHAR(10) NOT NULL');
    }
}
