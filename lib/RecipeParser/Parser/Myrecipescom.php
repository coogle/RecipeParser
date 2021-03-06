<?php

namespace RecipeParser\Parser;

class Myrecipescom {

    static public function parse(\DOMDocument $doc, $url) {
        // Get all of the standard microdata stuff we can find.
        $recipe = \RecipeParser\Parser\MicrodataDataVocabulary::parse($doc, $url);
        $xpath = new \DOMXPath($doc);

        // OVERRIDES FOR MYRECIPES.COM

        // Title missing?
        if (!$recipe->title) {
            $nodes = $xpath->query('//meta[@property="og:title"]');
            if ($nodes->length) {
                $line = $nodes->item(0)->getAttribute("content");
                $line = \RecipeParser\Text::formatTitle($line);
                $recipe->title = $line;
            }
        }
        
        $index = strpos($recipe->title, " Recipe | MyRecipes.com");
        if ($index) {
            $recipe->title = substr($recipe->title, 0, $index);
        }

        // Photo URL, use larger version found on MyRecipes
        $recipe->photo_url = str_replace('-l.jpg', '-x.jpg', $recipe->photo_url);

        // Credits
        $nodes = $xpath->query('//*[@class="link-list"]/h4');
        if ($nodes->length) {
            $line = trim($nodes->item(0)->nodeValue);
            if (strpos($line, "More from") === 0) {
                $line = str_replace("More from ", "", $line);
                $recipe->credits = $line;
            }
        }

        // Times
        $searches = array('prep' => 'prep: ',
                          'cook' => 'cook: ',
                          'total' => 'total: ');

        $nodes = $xpath->query('//*[@class="recipe-time-info"]');
        foreach ($nodes as $node) {
            $line = trim(strtolower($node->nodeValue));
            foreach ($searches as $key=>$value) {
                if (strpos($line, $value) === 0) {
                    $line = str_replace($value, "", $line);
                    $recipe->time[$key] = \RecipeParser\Times::toMinutes($line);
                }
            }
        }

        // Clean up each of the ingredients to remove "$Click to see savings"
        // These don't come through in the curl'ed test files
        for ($i = 0; $i < count($recipe->ingredients); $i++) {
            for ($j = 0; $j < count($recipe->ingredients[$i]['list']); $j++) {
                if (strpos($recipe->ingredients[$i]['list'][$j], "$") > 0) {
                    $recipe->ingredients[$i]['list'][$j] = substr($recipe->ingredients[$i]['list'][$j], 0, strpos($recipe->ingredients[$i]['list'][$j], "$"));
                }
            }
        }

        return $recipe;
    }

}
