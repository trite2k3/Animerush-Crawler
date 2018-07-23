<?php
//Variables, how many results to show, div id value and url's to crawl
$resultsVar = 1;
$crawlDivVar = 'episode_list';
$linksVar = array('http://www.animerush.tv/anime/Gamers/','http://www.animerush.tv/anime/Knights-Magic/','http://www.animerush.tv/anime/Fate-Apocrypha/','http://www.animerush.tv/anime/Enmusubi-no-Youko-chan/','http://www.animerush.tv/anime/Dungeon-ni-Deai-wo-Motomeru-no-wa-Machigatteiru-Darou-ka-Gaiden-Sword-Oratoria/','http://www.animerush.tv/anime/Little-Witch-Academia-TV/','http://www.animerush.tv/anime/Boku-no-Hero-Academia-2nd-Season/','http://www.animerush.tv/anime/Berserk-2017/','http://www.animerush.tv/anime/Youjo-Senki/','http://www.animerush.tv/anime/one-piece/','http://www.animerush.tv/anime/Dragon-Ball-Super/','http://www.animerush.tv/anime/Kono-Subarashii-Sekai-ni-Shukufuku-wo-2/','http://www.animerush.tv/anime/Kuzu-no-Honkai/','http://www.animerush.tv/anime/naruto-shippuuden/');
$latestEpisodesVar = 30;
$latestFilterVar = 'episode';

//POST changes to variables
if ($_POST['latestEpisodes'] || $_POST['crawlDivVar'] || $_POST['latestFilterVar'] || $_POST['resultsVar'] || $_POST['linksVar'])
{
	if (preg_match("/[^0-90-9'-]/",$_POST['latestEpisodes']))
	{
	die("Invalid input latestEpisodesVar.");
	}
	elseif (preg_match("/[^A-za-z_'-]/",$_POST['crawlDivVar']))
	{
        die("Invalid input crawlDivVar.");
        }
	elseif (preg_match("/[^A-za-z_'-]/",$_POST['latestFilterVar']))
	{
	die("Invalid input latestFilterVar.");
	}
	elseif (preg_match("/[^0-90-9'-]/",$_POST['resultsVar']))
	{
	die("Invalid input resultsVar.");
	}
	elseif (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i/m",$_POST['linksVar']))
	{
	die("Invalid input linksVar.");
	}
	$latestEpisodesVar = ($_POST['latestEpisodes']);
	$crawlDivVar = ($_POST['crawlDivVar']);
	$latestFilterVar = ($_POST['latestFilterVar']);
	$resultsVar = ($_POST['resultsVar']);
	$linksVar = explode(",", ($_POST['linksVar']));
}


function episodeCrawler($urlVar,$resultsVar,$filterVar)
{
//URL Variable into HTML
$html = file_get_contents($urlVar);
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
			//Trim result from unnecessary characters that we do not want "href=" and """
			$htmlString2trimhref = str_replace('href=', "", $htmlString2);
			$htmlString2trimquote = str_replace('"', "", $htmlString2trimhref);
			$htmlString3 = trim($htmlString2trimquote);
			//Get entire file into string
			$html3 = file_get_contents($htmlString3);
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
	<!-- COMMENT
	<div class="settings">
	<form action="<?php $_PHP_SELF ?>" method="POST">
	<textarea type="comment" name="linksVar" rows="10" cols="100" style="float: right;"><?php echo implode(",", $linksVar); ?></textarea><br/>
	<input type="text" name="latestEpisodes" value="<?php echo $latestEpisodesVar; ?>" />Latest episodes.<br/>
	<input type="text" name="crawlDivVar" value="<?php echo $crawlDivVar; ?>" />Crawl div-tag.<br/>
	<input type="text" name="latestFilterVar" value="<?php echo $latestFilterVar; ?>" />Crawl latest episodes div-tag.<br/>
	<input type="text" name="resultsVar" value="<?php echo $resultsVar; ?>" />Results from custom episodes to show.<br/>
	<input type="submit" name="submit" value="Submit" />
	</form>
	</div>
	END COMMENT -->	
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
