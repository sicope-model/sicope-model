<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211024150611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add default settings';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO craue_config_setting (name, value) VALUES (\'generator\', \'random\');');
        $this->addSql('INSERT INTO craue_config_setting (name, value) VALUES (\'reducer\', \'random\');');
        $this->addSql('INSERT INTO craue_config_setting (name, value) VALUES (\'report_bug\', \'0\');');
        $this->addSql('INSERT INTO craue_config_setting (name, value) VALUES (\'notify_author\', \'0\');');
        $this->addSql('INSERT INTO craue_config_setting (name, value) VALUES (\'notify_channels\', \'[]\');');
        $this->addSql('INSERT INTO craue_config_setting (name, value) VALUES (\'max_steps\', \'150\');');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM craue_config_setting WHERE name IN (\'generator\', \'reducer\', \'report_bug\', \'notify_author\', \'notify_channels\', \'max_steps\');');
    }
}
