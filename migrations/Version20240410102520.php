<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410102520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE field (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', type_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', value VARCHAR(255) NOT NULL, INDEX IDX_5BF54558C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_type (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_user (group_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_A4C98D39FE54D947 (group_id), INDEX IDX_A4C98D39A76ED395 (user_id), PRIMARY KEY(group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', group_user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', owner_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) NOT NULL, INDEX IDX_35C246D5216E8799 (group_user_id), INDEX IDX_35C246D57E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE share (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', password_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', expire_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EF069D5A3E4A79C1 (password_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email_verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', token VARCHAR(350) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558C54C8C93 FOREIGN KEY (type_id) REFERENCES field_type (id)');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE password ADD CONSTRAINT FK_35C246D5216E8799 FOREIGN KEY (group_user_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE password ADD CONSTRAINT FK_35C246D57E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE share ADD CONSTRAINT FK_EF069D5A3E4A79C1 FOREIGN KEY (password_id) REFERENCES password (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558C54C8C93');
        $this->addSql('ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947');
        $this->addSql('ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39A76ED395');
        $this->addSql('ALTER TABLE password DROP FOREIGN KEY FK_35C246D5216E8799');
        $this->addSql('ALTER TABLE password DROP FOREIGN KEY FK_35C246D57E3C61F9');
        $this->addSql('ALTER TABLE share DROP FOREIGN KEY FK_EF069D5A3E4A79C1');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE field_type');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE group_user');
        $this->addSql('DROP TABLE password');
        $this->addSql('DROP TABLE share');
        $this->addSql('DROP TABLE user');
    }
}
