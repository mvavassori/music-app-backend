<?php
namespace Models\Enums;

// enum works from php 8.1 and later. With older versions i should have done a class with const variables for genres.
enum Genre: string {
    case ROCK = 'rock';
    case POP = 'pop';
    case JAZZ = 'jazz';
    case CLASSICAL = 'classical';
    case HIPHOP = 'hip-hop';
    case ELECTRONIC = 'electronic';
    case COUNTRY = 'country';
    case RNB = 'r&b';
    case METAL = 'metal';
    case BLUES = 'blues';
    case REGGAE = 'reggae';
    case FOLK = 'folk';

    public static function getAll(): array {
        return array_column(self::cases(), "value"); // "value" is a special key that makes you access the string value
    }

    public static function isValid(string $genre): bool {
        return in_array($genre, self::getAll());
    }

    public static function fromStringToGenre($genre): Genre|null {
        foreach (self::cases() as $case) {
            if ($genre === $case->value) {
                return $case;
            }
        }
        return null;
    }
}