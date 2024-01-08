<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240107013815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE libro (id INT AUTO_INCREMENT NOT NULL, autores_id INT DEFAULT NULL, titulo VARCHAR(255) NOT NULL, sinopsis LONGTEXT NOT NULL, anio_publicacion INT NOT NULL, cantidad INT NOT NULL, INDEX IDX_5799AD2BC5CD6563 (autores_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE libro ADD CONSTRAINT FK_5799AD2BC5CD6563 FOREIGN KEY (autores_id) REFERENCES autor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE libro DROP FOREIGN KEY FK_5799AD2BC5CD6563');
        $this->addSql('DROP TABLE libro');
    }
}
