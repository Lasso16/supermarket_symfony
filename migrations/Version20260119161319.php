<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119161319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operaciones DROP FOREIGN KEY fk_operacion_producto');
        $this->addSql('ALTER TABLE operaciones DROP FOREIGN KEY fk_operacion_usuario');
        $this->addSql('DROP TABLE operaciones');
        $this->addSql('ALTER TABLE categorias DROP imagen, CHANGE nombre nombre VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY fk_producto_usuario');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY fk_producto_categoria');
        $this->addSql('ALTER TABLE productos CHANGE categoria_id categoria_id INT DEFAULT NULL, CHANGE titulo titulo VARCHAR(100) NOT NULL, CHANGE descripcion descripcion LONGTEXT DEFAULT NULL, CHANGE precio precio NUMERIC(10, 2) DEFAULT NULL, CHANGE imagen imagen VARCHAR(255) DEFAULT \'default_product.png\', CHANGE fecha_publicacion fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX fk_producto_usuario ON productos');
        $this->addSql('CREATE INDEX IDX_767490E6DB38439E ON productos (usuario_id)');
        $this->addSql('DROP INDEX fk_producto_categoria ON productos');
        $this->addSql('CREATE INDEX IDX_767490E63397707A ON productos (categoria_id)');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT fk_producto_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (id)');
        $this->addSql('ALTER TABLE usuarios ADD nombre_usuario VARCHAR(50) NOT NULL, ADD tipo VARCHAR(20) DEFAULT \'normal\' NOT NULL, DROP nombre, DROP roles, CHANGE email email VARCHAR(100) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX nombre_usuario ON usuarios (nombre_usuario)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operaciones (id INT AUTO_INCREMENT NOT NULL, comprador_id INT NOT NULL, producto_id INT NOT NULL, tipo VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'compra\' COLLATE `utf8mb4_unicode_ci`, fecha_operacion DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX fk_operacion_producto (producto_id), INDEX fk_operacion_usuario (comprador_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE operaciones ADD CONSTRAINT fk_operacion_producto FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE operaciones ADD CONSTRAINT fk_operacion_usuario FOREIGN KEY (comprador_id) REFERENCES usuarios (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE categorias ADD imagen VARCHAR(255) DEFAULT NULL, CHANGE nombre nombre VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY FK_767490E6DB38439E');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY FK_767490E63397707A');
        $this->addSql('ALTER TABLE productos CHANGE categoria_id categoria_id INT NOT NULL, CHANGE titulo titulo VARCHAR(150) NOT NULL, CHANGE descripcion descripcion TEXT DEFAULT NULL, CHANGE precio precio NUMERIC(10, 2) NOT NULL, CHANGE imagen imagen VARCHAR(255) DEFAULT \'product-default.png\', CHANGE fecha_creacion fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX idx_767490e63397707a ON productos');
        $this->addSql('CREATE INDEX fk_producto_categoria ON productos (categoria_id)');
        $this->addSql('DROP INDEX idx_767490e6db38439e ON productos');
        $this->addSql('CREATE INDEX fk_producto_usuario ON productos (usuario_id)');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT FK_767490E6DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT FK_767490E63397707A FOREIGN KEY (categoria_id) REFERENCES categorias (id)');
        $this->addSql('DROP INDEX nombre_usuario ON usuarios');
        $this->addSql('ALTER TABLE usuarios ADD nombre VARCHAR(100) NOT NULL, ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', DROP nombre_usuario, DROP tipo, CHANGE email email VARCHAR(180) NOT NULL');
    }
}
