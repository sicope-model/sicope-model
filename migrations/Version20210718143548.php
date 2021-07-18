<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210718143548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE activity_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_config_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE bug_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE data_table_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mail_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE model_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE revision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE widget_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE activity_log (id INT NOT NULL, owner_id INT DEFAULT NULL, method SMALLINT NOT NULL, uri VARCHAR(255) NOT NULL, body TEXT DEFAULT NULL, client_ip VARCHAR(45) NOT NULL, locale VARCHAR(2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FD06F6477E3C61F9 ON activity_log (owner_id)');
        $this->addSql('CREATE TABLE app_config (id INT NOT NULL, name VARCHAR(190) NOT NULL, value TEXT DEFAULT NULL, type VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_318942FC5E237E06 ON app_config (name)');
        $this->addSql('CREATE TABLE bug (id INT NOT NULL, task_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, steps TEXT NOT NULL, message TEXT NOT NULL, closed BOOLEAN NOT NULL, reducing BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, progress_total INT DEFAULT 0 NOT NULL, progress_processed INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_358CBF148DB60186 ON bug (task_id)');
        $this->addSql('COMMENT ON COLUMN bug.steps IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE data_table (id INT NOT NULL, owner_id INT NOT NULL, hidden TEXT DEFAULT NULL, orders TEXT DEFAULT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B89643D97E3C61F9 ON data_table (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX name_owner_unique ON data_table (name, owner_id)');
        $this->addSql('COMMENT ON COLUMN data_table.hidden IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN data_table.orders IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE mail_log (id INT NOT NULL, mail_to TEXT NOT NULL, mail_from TEXT NOT NULL, mail_cc TEXT DEFAULT NULL, mail_bcc TEXT DEFAULT NULL, mail_subject VARCHAR(255) DEFAULT NULL, mail_body TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN mail_log.mail_to IS \'(DC2Type:simple_array)\'');
        $this->addSql('COMMENT ON COLUMN mail_log.mail_from IS \'(DC2Type:simple_array)\'');
        $this->addSql('COMMENT ON COLUMN mail_log.mail_cc IS \'(DC2Type:simple_array)\'');
        $this->addSql('COMMENT ON COLUMN mail_log.mail_bcc IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE model (id INT NOT NULL, active_revision_id INT DEFAULT NULL, author INT DEFAULT NULL, label VARCHAR(255) NOT NULL, tags VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79572D9490589C4 ON model (active_revision_id)');
        $this->addSql('CREATE TABLE revision (id INT NOT NULL, model_id INT DEFAULT NULL, places TEXT NOT NULL, transitions TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D6315CC7975B7E7 ON revision (model_id)');
        $this->addSql('COMMENT ON COLUMN revision.places IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN revision.transitions IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, model_revision_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, author INT DEFAULT NULL, running BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, selenium_config_provider VARCHAR(255) NOT NULL, selenium_config_platform VARCHAR(255) NOT NULL, selenium_config_browser VARCHAR(255) NOT NULL, selenium_config_browser_version VARCHAR(255) NOT NULL, selenium_config_resolution VARCHAR(255) NOT NULL, task_config_generator VARCHAR(255) NOT NULL, task_config_generator_config TEXT NOT NULL, task_config_reducer VARCHAR(255) NOT NULL, task_config_notify_author BOOLEAN NOT NULL, task_config_notify_channels TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB2573F84563 ON task (model_revision_id)');
        $this->addSql('COMMENT ON COLUMN task.task_config_generator_config IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN task.task_config_notify_channels IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE user_group (id INT NOT NULL, name VARCHAR(180) NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F02BF9D5E237E06 ON user_group (name)');
        $this->addSql('COMMENT ON COLUMN user_group.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, locked BOOLEAN NOT NULL, password VARCHAR(100) NOT NULL, email VARCHAR(100) DEFAULT NULL, active BOOLEAN NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, language VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
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
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F6477E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bug ADD CONSTRAINT FK_358CBF148DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data_table ADD CONSTRAINT FK_B89643D97E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE model ADD CONSTRAINT FK_D79572D9490589C4 FOREIGN KEY (active_revision_id) REFERENCES revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE revision ADD CONSTRAINT FK_6D6315CC7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2573F84563 FOREIGN KEY (model_revision_id) REFERENCES revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_group_tax ADD CONSTRAINT FK_DF3DFEBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_group_tax ADD CONSTRAINT FK_DF3DFEBFE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE widget_user ADD CONSTRAINT FK_1564DDF67E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE revision DROP CONSTRAINT FK_6D6315CC7975B7E7');
        $this->addSql('ALTER TABLE model DROP CONSTRAINT FK_D79572D9490589C4');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB2573F84563');
        $this->addSql('ALTER TABLE bug DROP CONSTRAINT FK_358CBF148DB60186');
        $this->addSql('ALTER TABLE user_group_tax DROP CONSTRAINT FK_DF3DFEBFE54D947');
        $this->addSql('ALTER TABLE activity_log DROP CONSTRAINT FK_FD06F6477E3C61F9');
        $this->addSql('ALTER TABLE data_table DROP CONSTRAINT FK_B89643D97E3C61F9');
        $this->addSql('ALTER TABLE user_group_tax DROP CONSTRAINT FK_DF3DFEBA76ED395');
        $this->addSql('ALTER TABLE widget_user DROP CONSTRAINT FK_1564DDF67E3C61F9');
        $this->addSql('DROP SEQUENCE activity_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_config_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE bug_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE data_table_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mail_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE model_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE revision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE widget_user_id_seq CASCADE');
        $this->addSql('DROP TABLE activity_log');
        $this->addSql('DROP TABLE app_config');
        $this->addSql('DROP TABLE bug');
        $this->addSql('DROP TABLE data_table');
        $this->addSql('DROP TABLE mail_log');
        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE revision');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_group_tax');
        $this->addSql('DROP TABLE widget_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
