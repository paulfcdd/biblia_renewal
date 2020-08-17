<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200530120001 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE interpretation (id BIGINT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_group_translation (id BIGINT AUTO_INCREMENT NOT NULL, book_group_id BIGINT DEFAULT NULL, lang_id BIGINT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX book_group_translation_fk0 (book_group_id), INDEX book_group_translation_fk1 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id BIGINT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset_history (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, email VARCHAR(255) NOT NULL, client_ip VARCHAR(255) NOT NULL, requested_at DATETIME NOT NULL, INDEX password_reset_history_fk0 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_favourite_verse (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, verse_id BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX user_favourite_verse_fk0 (user_id), INDEX user_favourite_verse_fk1 (verse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_translation (id BIGINT AUTO_INCREMENT NOT NULL, book_id BIGINT DEFAULT NULL, lang_id BIGINT DEFAULT NULL, title varchar(255) NOT NULL, INDEX book_translation_fk0 (book_id), INDEX book_translation_fk1 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verse_translation (id BIGINT AUTO_INCREMENT NOT NULL, verse_id BIGINT DEFAULT NULL, lang_id BIGINT DEFAULT NULL, original_translation LONGTEXT NOT NULL, prepared_translation LONGTEXT NOT NULL, INDEX verse_translation_fk0 (verse_id), INDEX verse_translation_fk1 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section_translation (id BIGINT AUTO_INCREMENT NOT NULL, section_id BIGINT DEFAULT NULL, lang_id BIGINT NOT NULL, INDEX section_translation_fk0 (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verse (id BIGINT AUTO_INCREMENT NOT NULL, chapter_id BIGINT DEFAULT NULL, number VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX verse_fk0 (chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_title_variant (id BIGINT AUTO_INCREMENT NOT NULL, book_id BIGINT DEFAULT NULL, main_title VARCHAR(255) NOT NULL, chapter_title VARCHAR(255) NOT NULL, page_title VARCHAR(255) NOT NULL, INDEX book_title_variant_fk0 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_group (id BIGINT AUTO_INCREMENT NOT NULL, section_id BIGINT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX book_group_fk0 (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verse_origin_numbering (id BIGINT AUTO_INCREMENT NOT NULL, lang_id BIGINT DEFAULT NULL, verse_id BIGINT DEFAULT NULL, `from` VARCHAR(255) NOT NULL, `to` VARCHAR(255) NOT NULL, INDEX verse_origin_numbering_fk0 (lang_id), INDEX verse_origin_numbering_fk1 (verse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapter (id BIGINT AUTO_INCREMENT NOT NULL, book_id BIGINT DEFAULT NULL, number INT NOT NULL, INDEX chapter_fk0 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_bookmark (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, chapter_id BIGINT DEFAULT NULL, verse_id BIGINT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX user_bookmark_fk0 (user_id), INDEX user_bookmark_fk1 (chapter_id), INDEX user_bookmark_fk2 (verse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lang (id BIGINT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, native_title VARCHAR(255) NOT NULL, iso_code VARCHAR(10) NOT NULL, url_slug_code VARCHAR(10) NOT NULL, sort_order INT NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_audio_source (id BIGINT AUTO_INCREMENT NOT NULL, book_id BIGINT DEFAULT NULL, source VARCHAR(255) NOT NULL, INDEX book_audio_source_fk0 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id BIGINT AUTO_INCREMENT NOT NULL, book_group_id BIGINT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, abbreviation VARCHAR(255) NOT NULL, canonical TINYINT(1) NOT NULL, inscription TINYINT(1) NOT NULL, INDEX book_fk0 (book_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapter_interpretation (id BIGINT AUTO_INCREMENT NOT NULL, interpretation_id BIGINT DEFAULT NULL, chapter_id BIGINT DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX chapter_interpretation_fk0 (interpretation_id), INDEX chapter_interpretation_fk1 (chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verse_cross_reference (id BIGINT AUTO_INCREMENT NOT NULL, source_verse VARCHAR(255) NOT NULL, target_verse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_group_translation ADD CONSTRAINT FK_431B8C4CE7ABCF1E FOREIGN KEY (book_group_id) REFERENCES book_group (id)');
        $this->addSql('ALTER TABLE book_group_translation ADD CONSTRAINT FK_431B8C4CB213FA4 FOREIGN KEY (lang_id) REFERENCES lang (id)');
        $this->addSql('ALTER TABLE password_reset_history ADD CONSTRAINT FK_D9FD568EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_favourite_verse ADD CONSTRAINT FK_615E0B65A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_favourite_verse ADD CONSTRAINT FK_615E0B65BBF309FA FOREIGN KEY (verse_id) REFERENCES verse (id)');
        $this->addSql('ALTER TABLE book_translation ADD CONSTRAINT FK_E69E0A1316A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_translation ADD CONSTRAINT FK_E69E0A13B213FA4 FOREIGN KEY (lang_id) REFERENCES lang (id)');
        $this->addSql('ALTER TABLE verse_translation ADD CONSTRAINT FK_F37E7D47BBF309FA FOREIGN KEY (verse_id) REFERENCES verse (id)');
        $this->addSql('ALTER TABLE verse_translation ADD CONSTRAINT FK_F37E7D47B213FA4 FOREIGN KEY (lang_id) REFERENCES lang (id)');
        $this->addSql('ALTER TABLE section_translation ADD CONSTRAINT FK_A2983A10D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE verse ADD CONSTRAINT FK_D2F7E69F579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id)');
        $this->addSql('ALTER TABLE book_title_variant ADD CONSTRAINT FK_D6D6BED816A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_group ADD CONSTRAINT FK_630251BCD823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE verse_origin_numbering ADD CONSTRAINT FK_19FE1C85B213FA4 FOREIGN KEY (lang_id) REFERENCES lang (id)');
        $this->addSql('ALTER TABLE verse_origin_numbering ADD CONSTRAINT FK_19FE1C85BBF309FA FOREIGN KEY (verse_id) REFERENCES verse (id)');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE user_bookmark ADD CONSTRAINT FK_3AEF761DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_bookmark ADD CONSTRAINT FK_3AEF761D579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id)');
        $this->addSql('ALTER TABLE user_bookmark ADD CONSTRAINT FK_3AEF761DBBF309FA FOREIGN KEY (verse_id) REFERENCES verse (id)');
        $this->addSql('ALTER TABLE book_audio_source ADD CONSTRAINT FK_464B48D16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331E7ABCF1E FOREIGN KEY (book_group_id) REFERENCES book_group (id)');
        $this->addSql('ALTER TABLE chapter_interpretation ADD CONSTRAINT FK_5ACA475FF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id)');
        $this->addSql('ALTER TABLE chapter_interpretation ADD CONSTRAINT FK_5ACA475F579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE chapter_interpretation DROP FOREIGN KEY FK_5ACA475FF60A8F2C');
        $this->addSql('ALTER TABLE section_translation DROP FOREIGN KEY FK_A2983A10D823E37A');
        $this->addSql('ALTER TABLE book_group DROP FOREIGN KEY FK_630251BCD823E37A');
        $this->addSql('ALTER TABLE user_favourite_verse DROP FOREIGN KEY FK_615E0B65BBF309FA');
        $this->addSql('ALTER TABLE verse_translation DROP FOREIGN KEY FK_F37E7D47BBF309FA');
        $this->addSql('ALTER TABLE verse_origin_numbering DROP FOREIGN KEY FK_19FE1C85BBF309FA');
        $this->addSql('ALTER TABLE user_bookmark DROP FOREIGN KEY FK_3AEF761DBBF309FA');
        $this->addSql('ALTER TABLE password_reset_history DROP FOREIGN KEY FK_D9FD568EA76ED395');
        $this->addSql('ALTER TABLE user_favourite_verse DROP FOREIGN KEY FK_615E0B65A76ED395');
        $this->addSql('ALTER TABLE user_bookmark DROP FOREIGN KEY FK_3AEF761DA76ED395');
        $this->addSql('ALTER TABLE book_group_translation DROP FOREIGN KEY FK_431B8C4CE7ABCF1E');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331E7ABCF1E');
        $this->addSql('ALTER TABLE verse DROP FOREIGN KEY FK_D2F7E69F579F4768');
        $this->addSql('ALTER TABLE user_bookmark DROP FOREIGN KEY FK_3AEF761D579F4768');
        $this->addSql('ALTER TABLE chapter_interpretation DROP FOREIGN KEY FK_5ACA475F579F4768');
        $this->addSql('ALTER TABLE book_group_translation DROP FOREIGN KEY FK_431B8C4CB213FA4');
        $this->addSql('ALTER TABLE book_translation DROP FOREIGN KEY FK_E69E0A13B213FA4');
        $this->addSql('ALTER TABLE verse_translation DROP FOREIGN KEY FK_F37E7D47B213FA4');
        $this->addSql('ALTER TABLE verse_origin_numbering DROP FOREIGN KEY FK_19FE1C85B213FA4');
        $this->addSql('ALTER TABLE book_translation DROP FOREIGN KEY FK_E69E0A1316A2B381');
        $this->addSql('ALTER TABLE book_title_variant DROP FOREIGN KEY FK_D6D6BED816A2B381');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E16A2B381');
        $this->addSql('ALTER TABLE book_audio_source DROP FOREIGN KEY FK_464B48D16A2B381');
        $this->addSql('DROP TABLE interpretation');
        $this->addSql('DROP TABLE book_group_translation');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE password_reset_history');
        $this->addSql('DROP TABLE user_favourite_verse');
        $this->addSql('DROP TABLE book_translation');
        $this->addSql('DROP TABLE verse_translation');
        $this->addSql('DROP TABLE section_translation');
        $this->addSql('DROP TABLE verse');
        $this->addSql('DROP TABLE book_title_variant');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE book_group');
        $this->addSql('DROP TABLE verse_origin_numbering');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE user_bookmark');
        $this->addSql('DROP TABLE lang');
        $this->addSql('DROP TABLE book_audio_source');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE chapter_interpretation');
        $this->addSql('DROP TABLE verse_cross_reference');
    }
}
