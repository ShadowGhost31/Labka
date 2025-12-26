<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251226110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add контрольне завдання entities: categories, products, product_reviews';
    }

    public function up(Schema $schema): void
    {
        // categories
        $this->addSql("CREATE TABLE categories (id SERIAL NOT NULL, name VARCHAR(120) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_3AF346687E3C61F9 ON categories (name)");

        // products
        $this->addSql("CREATE TABLE products (id SERIAL NOT NULL, category_id INT NOT NULL, name VARCHAR(160) NOT NULL, price NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)");
        $this->addSql("ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

        // product_reviews
        $this->addSql("CREATE TABLE product_reviews (id SERIAL NOT NULL, product_id INT NOT NULL, author_id INT DEFAULT NULL, rating INT NOT NULL, comment TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_8F470A4E4584665A ON product_reviews (product_id)");
        $this->addSql("CREATE INDEX IDX_8F470A4EF675F31B ON product_reviews (author_id)");
        $this->addSql("ALTER TABLE product_reviews ADD CONSTRAINT FK_8F470A4E4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE product_reviews ADD CONSTRAINT FK_8F470A4EF675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE product_reviews');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE categories');
    }
}
