<?php

namespace RecipeParser\Parser;

class Recipecom {

    static public function parse(\DOMDocument $doc, $url) {
        // Get all of the standard microdata stuff we can find.
        $recipe = \RecipeParser\Parser\MicrodataSchema::parse($doc, $url);
        $xpath = new \DOMXPath($doc);

        // OVERRIDES FOR RECIPE.COM

        // Instructions
        $recipe->resetInstructions();
        $nodes = $xpath->query('//*[@itemprop="recipeInstructions"]');
        foreach ($nodes as $node) {
            $line = \RecipeParser\Text::formatAsOneLine($node->nodeValue);
            $recipe->appendInstruction($line);
        }

        return $recipe;
    }

}
