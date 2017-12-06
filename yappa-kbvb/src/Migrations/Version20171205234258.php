<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171205234258 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE members_entered CHANGE member_id member_id INT NOT NULL, CHANGE entered_at entered_at DATETIME NOT NULL, CHANGE item item INT NOT NULL, CHANGE birthdate birthdate DATETIME NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE members_entered CHANGE item item VARCHAR(255) NOT NULL COLLATE utf8_general_ci, CHANGE member_id member_id VARCHAR(255) NOT NULL COLLATE utf8_general_ci, CHANGE entered_at entered_at VARCHAR(255) NOT NULL COLLATE utf8_general_ci, CHANGE birthdate birthdate VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
    }
}
