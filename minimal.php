<?php
//Variables, how many results to show, div id value and url's to crawl
$resultsVar = 1;
$crawlDivVar = 'episode_list';
$linksVar = array('https://www.animerush.tv/anime/Dungeon-ni-Deai-wo-Motomeru-no-wa-Machigatteiru-Darou-ka-III/','https://www.animerush.tv/anime/Jujutsu-Kaisen-TV/','https://www.animerush.tv/anime/one-piece/','https://www.animerush.tv/anime/Enen-no-Shouboutai-Ni-no-Shou/','https://www.animerush.tv/anime/Black-Clover-TV/');
$latestEpisodesVar = 20;
$latestFilterVar = 'episode';


function episodeCrawler($urlVar,$resultsVar,$filterVar)
{
//URL Variable into HTML
$html = file_get_contents($urlVar);
//echo "<p> urlVar: ";
//echo $urlVar;
//echo "</p>";
//Create DOM-Obect
$dom = new DOMDocument();
@$dom->loadHTML($html);
//Create DOM XPath
$xpath = new DOMXPath($dom);
//Get div-classes with specific ID through DOM XQuery
$xpath_resultset =  $xpath->query("//div[@class='$filterVar']");
        //Loop through all the result from previous XPath Query
        for ($i = 0; $i < $xpath_resultset->length; $i++)
        {
        //Save object into string with HTML-format
        $htmlString = $dom->saveHTML($xpath_resultset->item($i));
                        //When loop has gone through more than specified value of how many results to show, stop loop
                if ($i >= $resultsVar)
                {
                break;
                }
                //If object in result has matching string "Coming Soon", do dateCountdownCrawler
                elseif (strpos($htmlString, '- Coming soon') !== false)
                {
                                //Print out episode/object in result
                $comingSoonEpisode = $htmlString;
                                //Call dateCountdownCrawler
                                dateCountdownCrawler($dom,$xpath,$filterVar,$i,$comingSoonEpisode);
                }
                else
                {
                //print results
                echo '<div class="customEpisodes">';
                echo $htmlString;
                echo '</div>';
                }
        }
}

function dateCountdownCrawler($dom,$xpath,$filterVar,$i,$comingSoonEpisode)
{
                        //Extra Crawl for countdown when episode releases
                        //Make new XPath Query, filtering out only links
                        $xpath_resultset2 = $xpath->query("//div[@class='$filterVar']//a/@href");
                        //Save object into string with HTML format
                        $htmlString2 = $dom->saveHTML($xpath_resultset2->item($i));
                        //echo $htmlString2;
                        //Trim result from unnecessary characters that we do not want "href=" and """
                        $htmlString2trimhref = str_replace('href=', "", $htmlString2);
                        //echo "href";
                        //echo $htmlString2trimhref;
                        //echo "quote";
                        $htmlString2trimquote = str_replace('"', "", $htmlString2trimhref);
                        //echo $htmlString2trimquote;
                        //echo "htmlstring3";
                        $htmlString2trimslash = substr($htmlString2trimquote, 3);
                        $htmlString3 = trim($htmlString2trimslash);
                        //echo "https://" . $htmlString3;
                        //Get entire file into string
                        $html3 = file_get_contents("https://" . $htmlString3);
                        //Create new DOM Object with newly created HTML-string
                        $dom3 = new DOMDocument();
                        @$dom3->loadHTML($html3);
                        $xpath3 = new DOMXPath($dom3);
                        //XPath Query to get all <script> elements in HTML
                        $xpath_resultset3 =  $xpath3->query('//body//script[not(@src)]');
                        //Save item 2 from XPath Query into string with HTML-format (item2[3]is what we want in this case)
                        $htmlString4 = $dom3->saveHTML($xpath_resultset3->item(2));
                        //Get the position of the first char that we want to get
                        $javascriptPOSVar = strpos($htmlString4, 'ts = new Date')+14;
                        //Concatentate all the character from startposition until we got the values we want into one variable/string
                        $javascriptVariable = $htmlString4[$javascriptPOSVar] . $htmlString4[$javascriptPOSVar+1] . $htmlString4[$javascriptPOSVar+2] . $htmlString4[$javascriptPOSVar+3] . $htmlString4[$javascriptPOSVar+4] . $htmlString4[$javascriptPOSVar+5] . $htmlString4[$javascriptPOSVar+6] . $htmlString4[$javascriptPOSVar+7] . $htmlString4[$javascriptPOSVar+8] . $htmlString4[$javascriptPOSVar+9] . $htmlString4[$javascriptPOSVar+10] . $htmlString4[$javascriptPOSVar+11] . $htmlString4[$javascriptPOSVar+12];
                        ?>
                        <div class="comingSoonEpisode"><?php echo $comingSoonEpisode;?><div data-countdown="<?php date_default_timezone_set('UTC');echo date('Y/m/d H:m:s', $javascriptVariable/1000);?>"></div></div>
                        <?php
}

function allLatestEpisodesCrawler($latestEpisodesVar,$latestFilterVar)
{
        //URL Variable into HTML
        $html = file_get_contents("http://www.animerush.tv/");
        //Create DOM-Obect
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        //Create DOM XPath
        $xpath = new DOMXPath($dom);
        //Get div-classes with specific ID through DOM XQuery
        $xpath_resultset =  $xpath->query("//div[@class='$latestFilterVar']");
                //Loop through all the result from previous XPath Query
                for ($i = 0; $i < $xpath_resultset->length; $i++)
                {
                        //Save object into string with HTML-format
                        $htmlString = $dom->saveHTML($xpath_resultset->item($i));
                        //Print out episode/object in result
                        echo '<div class="latestEpisodes">';
                        echo $htmlString;
                        echo '</div>';
                        //When loop has gone through more than specified value of how many results to show, stop loop, also linebreak
                        if ($i >= $latestEpisodesVar-1)
                        {
                                break;
                        }
                }
}

function printer($linksArr,$resultsVar,$crawlDivVar,$latestEpisodesVar,$latestFilterVar)
{
        //Loop through all URL's supplied from variable and crawl episodes for each one, also crawl a list of all latest episodes
        for ($x = 0; $x < count($linksArr); $x++)
    {
                episodeCrawler($linksArr[$x],$resultsVar,$crawlDivVar);
    }
        echo '<p>            </p>';
        echo '<div class="latestEpisodes">';
        echo '</br>Latest Episodes: </br>';
        echo '</div>';
        echo '<div class="comingSoonEpisode">';
        echo '</br>';
        echo '</div>';
    allLatestEpisodesCrawler($latestEpisodesVar,$latestFilterVar);
}
?>

<!doctype html>
<html>
        <head>
        <title>Crawler</title>
        <link rel="stylesheet" href="style.css">
        </head>
<body>
<script type="text/javascript" src="jquery-3.2.0.min.js"></script>
<script src="jquery.countdown-2.2.0/jquery.countdown.js"></script>

<div class="main">
                <p>Showing <?php echo count($linksVar); ?> custom defined anime's and <?php echo $latestEpisodesVar; ?> latest episodes.
                </p>
                        <?php printer($linksVar,$resultsVar,$crawlDivVar,$latestEpisodesVar,$latestFilterVar); ?>
                                <script type="text/javascript">
                                $('[data-countdown]').each(function() {
                                var $this = $(this), finalDate = $(this).data('countdown');
                                $this.countdown(finalDate, function(event) {
                                $this.html(event.strftime('%DD %HH %MM %SS'));
                                });
                                });
                                </script>
</div>
</body>
</html>
