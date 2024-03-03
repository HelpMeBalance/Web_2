<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302230415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande_panier (commande_id INT NOT NULL, panier_id INT NOT NULL, INDEX IDX_2698F62782EA2E54 (commande_id), INDEX IDX_2698F627F77D927C (panier_id), PRIMARY KEY(commande_id, panier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_panier ADD CONSTRAINT FK_2698F62782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_panier ADD CONSTRAINT FK_2698F627F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_panier DROP FOREIGN KEY FK_2698F62782EA2E54');
        $this->addSql('ALTER TABLE commande_panier DROP FOREIGN KEY FK_2698F627F77D927C');
        $this->addSql('DROP TABLE commande_panier');
    }
}
