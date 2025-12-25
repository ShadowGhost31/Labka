<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251225182554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attachments (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, path VARCHAR(512) NOT NULL, created_at DATETIME NOT NULL, uploaded_by_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_47C4FAD6A2B28FE8 (uploaded_by_id), INDEX IDX_47C4FAD68DB60186 (task_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, author_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_5F9E962AF675F31B (author_id), INDEX IDX_5F9E962A8DB60186 (task_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE labels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, color VARCHAR(16) DEFAULT NULL, UNIQUE INDEX UNIQ_B5D102115E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE project_members (id INT AUTO_INCREMENT NOT NULL, joined_at DATETIME NOT NULL, project_id INT NOT NULL, user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_D3BEDE9A166D1F9C (project_id), INDEX IDX_D3BEDE9AA76ED395 (user_id), INDEX IDX_D3BEDE9AD60322AC (role_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(160) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, INDEX IDX_5C93B3A47E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE task_labels (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_8E7886C28DB60186 (task_id), INDEX IDX_8E7886C233B92F39 (label_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE task_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, sort_order INT NOT NULL, UNIQUE INDEX UNIQ_B30F45D5E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tasks (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, due_at DATETIME DEFAULT NULL, project_id INT NOT NULL, status_id INT NOT NULL, creator_id INT NOT NULL, assignee_id INT DEFAULT NULL, INDEX IDX_50586597166D1F9C (project_id), INDEX IDX_505865976BF700BD (status_id), INDEX IDX_5058659761220EA6 (creator_id), INDEX IDX_5058659759EC7D60 (assignee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE time_entries (id INT AUTO_INCREMENT NOT NULL, minutes INT NOT NULL, work_date DATE NOT NULL, note VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_797F12A3A76ED395 (user_id), INDEX IDX_797F12A38DB60186 (task_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user_roles (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_54FCD59FA76ED395 (user_id), INDEX IDX_54FCD59FD60322AC (role_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(120) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE attachments ADD CONSTRAINT FK_47C4FAD6A2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE attachments ADD CONSTRAINT FK_47C4FAD68DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A8DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9AD60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A47E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE task_labels ADD CONSTRAINT FK_8E7886C28DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)');
        $this->addSql('ALTER TABLE task_labels ADD CONSTRAINT FK_8E7886C233B92F39 FOREIGN KEY (label_id) REFERENCES labels (id)');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_505865976BF700BD FOREIGN KEY (status_id) REFERENCES task_statuses (id)');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_5058659761220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_5058659759EC7D60 FOREIGN KEY (assignee_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE time_entries ADD CONSTRAINT FK_797F12A3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE time_entries ADD CONSTRAINT FK_797F12A38DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachments DROP FOREIGN KEY FK_47C4FAD6A2B28FE8');
        $this->addSql('ALTER TABLE attachments DROP FOREIGN KEY FK_47C4FAD68DB60186');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AF675F31B');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A8DB60186');
        $this->addSql('ALTER TABLE project_members DROP FOREIGN KEY FK_D3BEDE9A166D1F9C');
        $this->addSql('ALTER TABLE project_members DROP FOREIGN KEY FK_D3BEDE9AA76ED395');
        $this->addSql('ALTER TABLE project_members DROP FOREIGN KEY FK_D3BEDE9AD60322AC');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A47E3C61F9');
        $this->addSql('ALTER TABLE task_labels DROP FOREIGN KEY FK_8E7886C28DB60186');
        $this->addSql('ALTER TABLE task_labels DROP FOREIGN KEY FK_8E7886C233B92F39');
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_50586597166D1F9C');
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_505865976BF700BD');
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_5058659761220EA6');
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_5058659759EC7D60');
        $this->addSql('ALTER TABLE time_entries DROP FOREIGN KEY FK_797F12A3A76ED395');
        $this->addSql('ALTER TABLE time_entries DROP FOREIGN KEY FK_797F12A38DB60186');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FD60322AC');
        $this->addSql('DROP TABLE attachments');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE labels');
        $this->addSql('DROP TABLE project_members');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE task_labels');
        $this->addSql('DROP TABLE task_statuses');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE time_entries');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
