<?php

class RecipeParser_Parser_Hungrycouplenyccom {
    
    static public function parse($html, $url) {
        $recipe = RecipeParser_Parser_Microformat::parse($html, $url);

        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($doc);
        
        // Title
        $nodes = $xpath->query('.//*[@itemprop="name"]', $microdata);
        if ($nodes->length) {
            $value = $nodes->item(0)->nodeValue;
            $value = RecipeParser_Text::formatTitle($value);
            $recipe->title = $value;
        }
        
        return $recipe;
    }
}
