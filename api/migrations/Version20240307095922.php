<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307095922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds a default user to the user table and a couple of greetings for testing purposes.';
    }

    public function up(Schema $schema): void
    {
        // The password is "password123"
        $this->addSql('INSERT INTO user (email, password, roles) 
            VALUES ("test@test.com", "$2y$13$N/izEwIp/wPmZ.7GfmZNa.ta/I6NzeSNO4GSmUdSASjyebZrwolzq", \'["ROLE_USER"]\')');
        $this->addSql('INSERT INTO greeting (name) VALUES ("Hello!")');
        $this->addSql('INSERT INTO greeting (name) VALUES ("Hi!")');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM user WHERE email = "test@test.com"');
        $this->addSql('DELETE FROM greeting WHERE name = "Hello!"');
        $this->addSql('DELETE FROM greeting WHERE name = "Hi!"');
    }
}
