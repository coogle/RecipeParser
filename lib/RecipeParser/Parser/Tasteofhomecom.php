<?php

namespace RecipeParser\Parser;

class Tasteofhomecom {

    static public function parse(\DOMDocument $doc, $url) {
        // Get all of the standard microdata stuff we can find.
        $recipe = \RecipeParser\Parser\MicrodataSchema::parse($doc, $url);
        $xpath = new \DOMXPath($doc);

        // OVERRIDES FOR TASTEOFHOME.COM

        // Notes
        $nodes = $xpath->query('//div[@class="rd_editornote margin_bottom"]');
        if ($nodes->length) {
            $line = $nodes->item(0)->nodeValue;
            $line = \RecipeParser\Text::formatAsOneLine($line);
            $line = preg_replace("/Editor's Note:\s+/", "", $line);
            $recipe->notes = $line;
        }

        // Override image
        $nodes = $xpath->query('//meta[@itemprop="image"]');
        if ($nodes->length) {
            $line = $nodes->item(0)->getAttribute("content");
            $recipe->photo_url = $line;
        }

        return $recipe;
    }

}

?>
