<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251225233000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tests table for API Platform CRUD (Lab 6)';
    }

    public function up(Schema $schema): void
    {
        // PostgreSQL: UUID type is supported natively.
        $this->addSql('CREATE TABLE test (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE tests');
    }
}
