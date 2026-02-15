<?php

namespace App\Traits\Models;

use Log;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Str;
trait AutoTranslate
{
    private function buildTranslation($locale): array
    {
        $defaultLocale = config('app.locale');
        $translatedObject = [];
        foreach ($this->translatedAttributes as $attribute) {
            try {

                sleep(0.1);

                $translation = GoogleTranslate::trans(
                    string: $this->translate($defaultLocale)->$attribute ?? '',
                    target: $locale,
                    source: $defaultLocale
                );

                $translatedObject[$attribute] = $attribute == 'slug' ? Str::slug($translation) : $translation;

            } catch (\Exception $exception) {
                Log::error("Can't translate This " . class_basename($this), [
                    'locale' => $locale,
                    'id' => $this->$this->getKey()
                ]);
            }
        }
        return $translatedObject;
    }

    public function autoTranslate(): void
    {

        $locales = array_filter(
            config('translatable.locales'),
            fn($locale) => $locale != config('app.locale')
        );

        foreach ($locales as $locale) {
            $translatedObject = $this->buildTranslation($locale);

            if (!empty($translatedObject)) {
                $this->update([
                    $locale => array_filter($translatedObject, fn($value) => !empty($value))
                ]);
            }
        }
    }
}
