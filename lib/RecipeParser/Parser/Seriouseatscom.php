<?php

namespace RecipeParser\Parser;

class Seriouseatscom {

    static public function parse(\DOMDocument $doc, $url) {
        // Get all of the standard microformat stuff we can find.
        $recipe = \RecipeParser\Parser\Microformat::parse($doc, $url);
        $xpath = new \DOMXPath($doc);

        // OVERRIDES FOR SERIOUSEATS.COM

        $hrecipe = $xpath->query('//*[@class="hrecipe"]');
        if ($hrecipe->length) {
            $hrecipe = $hrecipe->item(0);

            // Title is not marked up with class="fn"
            $nodes = $xpath->query('.//h1', $hrecipe);
            if ($nodes->length) {
                $value = $nodes->item(0)->nodeValue;
                $recipe->title = \RecipeParser\Text::formatTitle($value);
            }

            // Yield -- Class names are conflated
            $nodes = $xpath->query('.//*[@class="info yield"]', $hrecipe);
            if ($nodes->length) {
                $line = $nodes->item(0)->nodeValue;
                $recipe->yield = \RecipeParser\Text::formatYield($line);
            }

            // Prep Times -- Class names are conflated
            $nodes = $xpath->query('.//*[@class="info preptime"]', $hrecipe);
            if ($nodes->length) {
                $value = $nodes->item(0)->nodeValue;
                $recipe->time['prep'] = \RecipeParser\Times::toMinutes($value);
            }

            // Total Time / Duration -- Class names are conflated
            $nodes = $xpath->query('.//*[@class="info duration"]', $hrecipe);
            if ($nodes->length) {
                $value = $nodes->item(0)->nodeValue;
                $recipe->time['total'] = \RecipeParser\Times::toMinutes($value);
            }
        }

        // Photo
        $nodes = $xpath->query('//section[@class="content-unit"]/img');
        if ($nodes->length) {
            $photo_url = $nodes->item(0)->getAttribute('src');
            if ($photo_url) {
                $recipe->photo_url = \RecipeParser\Text::relativeToAbsolute($photo_url, $url);
            }
        }

        // Remove recipe title intros -- e.g. "Sunday Dinner: Pork Ribs" changes to "Pork Ribs"
        if (strpos($recipe->title, ": ") !== false) {
            $recipe->title = preg_replace("/^[^:]+: (.+)/", "$1", $recipe->title);
        }

        return $recipe;
    }

}

?>
