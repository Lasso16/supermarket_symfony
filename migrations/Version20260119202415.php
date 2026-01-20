<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119202415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compra_productos (id INT AUTO_INCREMENT NOT NULL, compra_id INT NOT NULL, producto_id INT NOT NULL, cantidad INT NOT NULL, precio_unitario NUMERIC(10, 2) NOT NULL, INDEX IDX_EA1E78B6F2E704D7 (compra_id), INDEX IDX_EA1E78B67645698E (producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compras (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, fecha_compra DATETIME NOT NULL, total NUMERIC(10, 2) NOT NULL, estado VARCHAR(20) NOT NULL, INDEX IDX_3692E1B7DB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compra_productos ADD CONSTRAINT FK_EA1E78B6F2E704D7 FOREIGN KEY (compra_id) REFERENCES compras (id)');
        $this->addSql('ALTER TABLE compra_productos ADD CONSTRAINT FK_EA1E78B67645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE compras ADD CONSTRAINT FK_3692E1B7DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compra_productos DROP FOREIGN KEY FK_EA1E78B6F2E704D7');
        $this->addSql('ALTER TABLE compra_productos DROP FOREIGN KEY FK_EA1E78B67645698E');
        $this->addSql('ALTER TABLE compras DROP FOREIGN KEY FK_3692E1B7DB38439E');
        $this->addSql('DROP TABLE compra_productos');
        $this->addSql('DROP TABLE compras');
    }
}
