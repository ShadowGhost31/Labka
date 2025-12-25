<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251225220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add password field to users table for JWT authentication';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE users ADD password VARCHAR(255) NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE users DROP password");
    }
}
