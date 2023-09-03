<?php

namespace app\helpers;

use app\models\profanity_words; // Подключаем модель для таблицы profanity_words

class WordFilterHelper
{
    /**
     * Заменяет матерные слова на ...
     * @param string $text текст, который нужно фильтровать
     * @return string отфильтрованный текст
     */
    private static function filterProfanity($text)
    {
        // Получаем список матерных слов из модели
        $profanityWords = profanity_words::find()->select('word')->column();

        // Проходимся по каждому матерному слову и заменяем его на ...
        foreach ($profanityWords as $word) {
            $text = preg_replace("/\b" . preg_quote($word, '/') . "\b/iu", '...', $text);
        }

        return $text;
    }

    /**
     * Заменяет ссылки на тег <a>
     * @param string $text текст, в которм нужно искать ссылки
     * @return string отфильтрованный текст
     */
    private static function replaceUrlsWithLinks($text) {
        // Регулярное выражение для поиска URL в тексте
        $pattern = '/(https?:\/\/\S+)/i';
    
        // Заменяем найденные URL на теги <a>
        $text = preg_replace($pattern, '<a href="$1">$1</a>', $text);
    
        return $text;
    }

    /**
     * удаляем тег <img>
     * @param string $text текст, в которм нужно искать тег <img>
     * @return string отфильтрованный текст
     */
    private static function removeImgTags($html) {
        // Удалить все теги <img> из HTML-кода
        $html = preg_replace('/<img\b[^>]*>/', '', $html);
        
        return $html;
    }

    /**
     * форматируем текст используя наши фунцкии в этом классе
     * @param string $text текст для форматирования
     * @return string форматированый текст
     */
    public static function formatText($text){
        $text = static::filterProfanity($text);
        $text = static::replaceUrlsWithLinks($text);
        $text = static::removeImgTags($text);
        return $text;
    }
}
