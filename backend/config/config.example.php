<?php

class Config
{
    public static function DB_HOST(): string { return 'localhost'; }
    public static function DB_NAME(): string { return 'Petly'; }
    public static function DB_USER(): string { return 'your_db_user'; }
    public static function DB_PASSWORD(): string { return 'your_db_password'; }

    public static function DOG_API_URL(): string { return 'https://api.thedogapi.com/v1'; }
    public static function DOG_API_KEY(): string { return 'your_thedogapi_key'; }
    public static function CAT_API_URL(): string { return 'https://api.thecatapi.com/v1'; }
    public static function CAT_API_KEY(): string { return 'your_thecatapi_key'; }
    public static function EXTERNAL_API_TIMEOUT(): int { return 5; }
}
