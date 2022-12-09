<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221208165510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_billetique_card ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_billetique_card ADD CONSTRAINT FK_D94A27809395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id)');
        $this->addSql('CREATE INDEX IDX_D94A27809395C3F3 ON app_billetique_card (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_billetique_card DROP FOREIGN KEY FK_D94A27809395C3F3');
        $this->addSql('DROP INDEX IDX_D94A27809395C3F3 ON app_billetique_card');
        $this->addSql('ALTER TABLE app_billetique_card DROP customer_id');
    }
}
