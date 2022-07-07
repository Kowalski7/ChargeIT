<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220707190452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C358D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7A853C358D93D649 ON bookings (user)');
        $this->addSql('ALTER TABLE plugs CHANGE station station CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE plugs RENAME INDEX plugs_fk TO Plugs_fk0');
        $this->addSql('ALTER TABLE stations CHANGE uuid uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE user_car MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user_car DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_car DROP id');
        $this->addSql('ALTER TABLE user_car ADD PRIMARY KEY (user, car)');
        $this->addSql('ALTER TABLE user_car RENAME INDEX user_car_fk0 TO IDX_9C2B87168D93D649');
        $this->addSql('ALTER TABLE user_car RENAME INDEX user_car_fk1 TO IDX_9C2B8716773DE69D');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C358D93D649');
        $this->addSql('DROP INDEX IDX_7A853C358D93D649 ON bookings');
        $this->addSql('ALTER TABLE plugs CHANGE station station CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE plugs RENAME INDEX plugs_fk0 TO plugs_FK');
        $this->addSql('ALTER TABLE stations CHANGE uuid uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE user_car ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user_car RENAME INDEX idx_9c2b87168d93d649 TO User_Car_fk0');
        $this->addSql('ALTER TABLE user_car RENAME INDEX idx_9c2b8716773de69d TO User_Car_fk1');
    }
}
