<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210117032521 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE app_config_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE bug_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mail_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mail_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE model_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE widget_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_config (id INT NOT NULL, name VARCHAR(190) NOT NULL, value TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_318942FC5E237E06 ON app_config (name)');
        $this->addSql('COMMENT ON COLUMN app_config.value IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE bug (id INT NOT NULL, task_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, steps TEXT NOT NULL, message TEXT NOT NULL, closed BOOLEAN NOT NULL, model_version INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, progress_total INT DEFAULT 0 NOT NULL, progress_processed INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_358CBF148DB60186 ON bug (task_id)');
        $this->addSql('COMMENT ON COLUMN bug.steps IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE mail_log (id INT NOT NULL, mTo TEXT NOT NULL, mFrom TEXT NOT NULL, subject VARCHAR(255) DEFAULT NULL, body TEXT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, template_id VARCHAR(75) DEFAULT NULL, language VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN mail_log.mTo IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN mail_log.mFrom IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN mail_log.body IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE mail_template (id INT NOT NULL, template_id VARCHAR(50) NOT NULL, template TEXT DEFAULT NULL, template_data TEXT DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, language VARCHAR(3) NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN mail_template.template_data IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE model (id INT NOT NULL, author INT DEFAULT NULL, label VARCHAR(255) NOT NULL, tags VARCHAR(255) DEFAULT NULL, start_commands TEXT NOT NULL, places TEXT NOT NULL, transitions TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN model.start_commands IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN model.places IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN model.transitions IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, model_id INT NOT NULL, title VARCHAR(255) NOT NULL, author INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, selenium_config_provider VARCHAR(255) NOT NULL, selenium_config_platform VARCHAR(255) NOT NULL, selenium_config_browser VARCHAR(255) NOT NULL, selenium_config_browser_version VARCHAR(255) NOT NULL, selenium_config_resolution VARCHAR(255) NOT NULL, task_config_generator VARCHAR(255) NOT NULL, task_config_generator_config TEXT NOT NULL, task_config_reducer VARCHAR(255) NOT NULL, task_config_notify_author BOOLEAN NOT NULL, task_config_notify_channels TEXT NOT NULL, progress_total INT DEFAULT 0 NOT NULL, progress_processed INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB257975B7E7 ON task (model_id)');
        $this->addSql('COMMENT ON COLUMN task.task_config_generator_config IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN task.task_config_notify_channels IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE user_group (id INT NOT NULL, name VARCHAR(180) NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F02BF9D5E237E06 ON user_group (name)');
        $this->addSql('COMMENT ON COLUMN user_group.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE user_profile (id INT NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, phone VARCHAR(15) DEFAULT NULL, website VARCHAR(100) DEFAULT NULL, company VARCHAR(100) DEFAULT NULL, language VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, profile_id INT DEFAULT NULL, password VARCHAR(98) NOT NULL, email VARCHAR(60) NOT NULL, is_active BOOLEAN NOT NULL, is_freeze BOOLEAN NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_login_ip VARCHAR(32) DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9CCFA12B8 ON users (profile_id)');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE user_group_tax (user_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY(user_id, group_id))');
        $this->addSql('CREATE INDEX IDX_DF3DFEBA76ED395 ON user_group_tax (user_id)');
        $this->addSql('CREATE INDEX IDX_DF3DFEBFE54D947 ON user_group_tax (group_id)');
        $this->addSql('CREATE TABLE widget_user (id INT NOT NULL, owner_id INT DEFAULT NULL, config TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1564DDF67E3C61F9 ON widget_user (owner_id)');
        $this->addSql('COMMENT ON COLUMN widget_user.config IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('BEGIN;');
        $this->addSql('LOCK TABLE messenger_messages;');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('COMMIT;');
        $this->addSql('ALTER TABLE bug ADD CONSTRAINT FK_358CBF148DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9CCFA12B8 FOREIGN KEY (profile_id) REFERENCES user_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_group_tax ADD CONSTRAINT FK_DF3DFEBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_group_tax ADD CONSTRAINT FK_DF3DFEBFE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE widget_user ADD CONSTRAINT FK_1564DDF67E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB257975B7E7');
        $this->addSql('ALTER TABLE bug DROP CONSTRAINT FK_358CBF148DB60186');
        $this->addSql('ALTER TABLE user_group_tax DROP CONSTRAINT FK_DF3DFEBFE54D947');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9CCFA12B8');
        $this->addSql('ALTER TABLE user_group_tax DROP CONSTRAINT FK_DF3DFEBA76ED395');
        $this->addSql('ALTER TABLE widget_user DROP CONSTRAINT FK_1564DDF67E3C61F9');
        $this->addSql('DROP SEQUENCE app_config_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE bug_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mail_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mail_template_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE model_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_profile_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE widget_user_id_seq CASCADE');
        $this->addSql('DROP TABLE app_config');
        $this->addSql('DROP TABLE bug');
        $this->addSql('DROP TABLE mail_log');
        $this->addSql('DROP TABLE mail_template');
        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE user_profile');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_group_tax');
        $this->addSql('DROP TABLE widget_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
