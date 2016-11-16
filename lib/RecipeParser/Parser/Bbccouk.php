<?php

namespace RecipeParser\Parser;

class Bbccouk {

    static public function parse(\DOMDocument $doc, $url) {
        // Get all of the standard microformat stuff we can find.
        $recipe = \RecipeParser\Parser\Microformat::parse($doc, $url);
        $xpath = new \DOMXPath($doc);

        // OVERRIDES FOR BDCCO.UK

        // Multi-stage ingredients
        $nodes = $xpath->query('//dl[@id="stages"]/*');
        if ($nodes->length) {
            $recipe->resetIngredients();

            foreach ($nodes as $node) {
                if ($node->nodeName == 'dt') {
                    $value = $node->nodeValue;
                    $value = \RecipeParser\Text::formatSectionName($value);
                    $recipe->addIngredientsSection($value);
                
                } else if ($node->nodeName == 'dd') {
                    $subs = $xpath->query('.//*[@class="ingredient"]', $node);
                    foreach ($subs as $sub) {
                        $value = trim($sub->nodeValue);
                        $recipe->appendIngredient($value);
                    }
                }

            }

        }

        return $recipe;
    }

}

?>
