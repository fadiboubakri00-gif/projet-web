<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114203943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, UNIQUE INDEX UNIQ_OWNER (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_product (cart_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_2890CCAA1AD5CDBF (cart_id), INDEX IDX_2890CCAA4584665A (product_id), PRIMARY KEY(cart_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, order_user_id INT NOT NULL, INDEX IDX_F529939851147ADE (order_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_product (order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_2530ADE68D9F6D38 (order_id), INDEX IDX_2530ADE64584665A (product_id), PRIMARY KEY(order_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B77E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT FK_2890CCAA1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT FK_2890CCAA4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939851147ADE FOREIGN KEY (order_user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE68D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B77E3C61F9');
        $this->addSql('ALTER TABLE cart_product DROP FOREIGN KEY FK_2890CCAA1AD5CDBF');
        $this->addSql('ALTER TABLE cart_product DROP FOREIGN KEY FK_2890CCAA4584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939851147ADE');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE68D9F6D38');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_product');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
