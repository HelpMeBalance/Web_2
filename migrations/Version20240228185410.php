<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228185410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, prix_tot DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, INDEX IDX_24CC0DF27294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF27294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE dossier_medicale DROP FOREIGN KEY FK_4C53FBC0CF18BB82');
        $this->addSql('ALTER TABLE dossier_medicale DROP FOREIGN KEY FK_4C53FBC01E27F6BF');
        $this->addSql('DROP TABLE dossier_medicale');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dossier_medicale (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, reponse_id INT NOT NULL, UNIQUE INDEX UNIQ_4C53FBC0CF18BB82 (reponse_id), UNIQUE INDEX UNIQ_4C53FBC01E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dossier_medicale ADD CONSTRAINT FK_4C53FBC0CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id)');
        $this->addSql('ALTER TABLE dossier_medicale ADD CONSTRAINT FK_4C53FBC01E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF27294869C');
        $this->addSql('DROP TABLE panier');
    }
}
