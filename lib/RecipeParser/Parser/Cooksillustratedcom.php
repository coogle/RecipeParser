<?php

namespace RecipeParser\Parser;

class Cooksillustratedcom {

    static public function parse(\DOMDocument $doc, $url) {
        $recipe = new \RecipeParser\Recipe();
        $xpath = new \DOMXPath($doc);

        // OVERRIDES FOR COOKSILLUSTRATED.COM

        // Title
        $nodes = $xpath->query('//div[@id="rightCol"]/h1');
        if ($nodes->length) {
            $recipe->title = trim($nodes->item(0)->nodeValue);
        }

        // Yield
        $nodes = $xpath->query('//h4[@class="detailHeader"]');
        if ($nodes->length) {
            $line = trim($nodes->item(0)->nodeValue);
            $recipe->yield = \RecipeParser\Text::formatYield($line);
        }

        // Notes
        $nodes = $xpath->query('//div[@class="dek"]');
        if ($nodes->length) {
            $line = trim($nodes->item(0)->nodeValue);
            $recipe->notes = $line;
        }

        // Ingredients
        $nodes = $xpath->query('//ul[@class="recipe_ingredients"]/li');
        foreach ($nodes as $node) {

            // Section names have class="ingredientSectionTitle",
            // ingredients themselves have no class.
            if ($node->hasAttributes()) {
                $line = trim($node->nodeValue);
                $line = \RecipeParser\Text::formatSectionName($line);
                $recipe->addIngredientsSection($line);
            } else {
                $line = trim($node->nodeValue);

                // Add spaces between quantities and units
                $line = preg_replace('/(\d+)([A-Za-z]+)/', "$1 $2", $line);

                // Remove spaces before commas (not sure why this happens in their HTML)
                $line = str_replace(' ,', ',', $line);

                // Condense multiple spaces
                $line = str_replace('  ', ' ', $line);

                $recipe->appendIngredient($line);
            }

        }

        // Instructions
        $nodes = $xpath->query('//ol[@class="recipe_instructions"]/li');
        foreach ($nodes as $node) {
            $line = trim($node->nodeValue);
            $line = \RecipeParser\Text::stripLeadingNumbers($line);
            $recipe->appendInstruction($line);
        }

        // Photo
        $nodes = $xpath->query('//img[@class="recipeImg"]');
        if ($nodes->length) {
            $photo_url = $nodes->item(0)->getAttribute('src');
            $recipe->photo_url = \RecipeParser\Text::relativeToAbsolute($photo_url, $url);
        } else {
            // Second option for where to find recipe image
            $nodes = $xpath->query('//img[@id="splashImage"]');
            if ($nodes->length) {
                $photo_url = $nodes->item(0)->getAttribute('src');
                $recipe->photo_url = \RecipeParser\Text::relativeToAbsolute($photo_url, $url);
            }
        }

        return $recipe;
    }

}
