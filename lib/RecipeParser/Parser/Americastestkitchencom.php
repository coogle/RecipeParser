<?php

namespace RecipeParser\Parser;

class Americastestkitchencom {

    static public function parse(\DOMDocument $doc, $url) {
        // Get all of the standard microdata stuff we can find.
        $recipe = \RecipeParser\Parser\MicrodataSchema::parse($doc, $url);
        $xpath = new \DOMXPath($doc);

        // OVERRIDES FOR AMERICASTESTKITCHEN.COM

        return $recipe;
    }

}
