<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228222221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F77D927C');
        $this->addSql('DROP INDEX IDX_23A0E66F77D927C ON article');
        $this->addSql('ALTER TABLE article DROP panier_id');
        $this->addSql('ALTER TABLE panier ADD article_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF27294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF27294869C ON panier (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD panier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66F77D927C ON article (panier_id)');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF27294869C');
        $this->addSql('DROP INDEX IDX_24CC0DF27294869C ON panier');
        $this->addSql('ALTER TABLE panier DROP article_id');
    }
}
