<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210116162546 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paziente (id INT AUTO_INCREMENT NOT NULL, cognome VARCHAR(50) NOT NULL, nome VARCHAR(50) NOT NULL, data_nascita DATE NOT NULL, sesso VARCHAR(1) NOT NULL, telefono VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ricovero (id INT AUTO_INCREMENT NOT NULL, paziente_id INT NOT NULL, data DATE NOT NULL, reparto VARCHAR(255) NOT NULL, data_dimissione DATE DEFAULT NULL, INDEX IDX_C0374CEC73732EFE (paziente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ricovero ADD CONSTRAINT FK_C0374CEC73732EFE FOREIGN KEY (paziente_id) REFERENCES paziente (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ricovero DROP FOREIGN KEY FK_C0374CEC73732EFE');
        $this->addSql('DROP TABLE paziente');
        $this->addSql('DROP TABLE ricovero');
    }
}
