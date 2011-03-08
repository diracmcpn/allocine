<?php

/**
 * \class Webpage
 * \brief class to communicate with a http host 
 *
 * Enable us to get website html data from an url
 */
class WebPage
{
	private $url; 

     public function __construct($url)
     {
		$this->url = $url;
     }

     public function getHtmlData($timeout = 10, $referer = "http://www.google.com/", $userAgent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)")
     {
	  	$handle = curl_init($this->url);
     
	  	if ($handle!=false)
	  	{
	       	//---cUrl Options---
	       	curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);//maximum time in seconds to allow cUrl functions to execute, by default 10s
	       	curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
	       	curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
	       	curl_setopt($handle, CURLOPT_REFERER, $referer);
	       	curl_setopt($handle, CURLOPT_USERAGENT, $userAgent); //By default, User Agent is IE in XP SP2

	       	$data = curl_exec($handle);
	       	curl_close($handle);
	       
	       	return $data;
	  	}
	  	else
	      	 return false;
     }
}

/**
 * \class AllocineDatabase
 * \brief class to get informations on Allocine.fr 
 *
 * Enable us to use Allocine.fr to look for a movie
 */
class AllocineDatabase
{

	private $nbTotalMovies;

	public function __construct()
     {
		$this->nbTotalMovies = 0; //init
     }

     private function getHtmlData($movieName, $pageNumber)
     {
	  	$name = urlencode($movieName);
		$number = urlencode($pageNumber);
	  	$url = "http://www.allocine.fr/recherche/1/?p=".$number."&q=".$name;
	  	$webpage = new WebPage($url);
	  	$data = $webpage->getHtmlData();

		return $data;
	}

	private function parseMoviesInArray(&$movie, &$data)
	{
		if(preg_match("#(\d+) - (\d+) sur \d+ résultats#", $data, $nbs))
		{	
			$begin=$nbs[1];
			$end=$nbs[2];
			$nbMovies = $end-$begin+1;

			$regex = "#".str_repeat("<tr><td.+\s*<a href='(.+)'><img\s*.+\s*src=.+\s*alt='(.+)' />(?:.*\s*){20,21}", $nbMovies)."#";
  			preg_match($regex, $data, $matches);

			for ($i=1;$i<=$nbMovies;$i++)
			{
				$title = $matches[2*$i];
				$url = "http://www.allocine.fr".$matches[2*$i-1];
				$movie[]=array('title' => $title, 'url' => $url);
			}
			
			return true;
		}
		else
			return false;
	}
	
	public function seekMovie($movieName)
	{
		$pageNumber = 1;
		$data = $this->getHtmlData($movieName, strval($pageNumber));

		if(preg_match("#(\d+) résultats trouvés dans les titres de films#", $data, $nb))
			$this->nbTotalMovies = $nb[1];
		else 
			$this->nbTotalMovies = 0;
		
		$movie = array(); //Save title and url
		
	  	//get the number of movies per page and parse them
		if ($this->nbTotalMovies!=0)
		{
			while($this->parseMoviesInArray($movie, $data))
			{
				$pageNumber=$pageNumber+1;
				$data = $this->getHtmlData($movieName, strval($pageNumber));
			}
		}
		return $movie;
     }

	public function getMoviesTotalNumber()
	{
		return $this->nbTotalMovies;
	}

}

/**
 * \class AllocineMovie
 * \brief class to get informations on Allocine.fr 
 *
 * Enable us to use Allocine.fr to get informations about movie
 */
class AllocineMovie
{

	public function getHtmlData($url)
     {
	  	$page = new WebPage($url);
	  	$data = $page->getHtmlData();
	  	return $data;
     }
     
     public function parseReleaseDate(&$data)
     {
	  	if ($data!=false)
	  	{
	       	if (preg_match("#Date de sortie cinéma :  <span class=\"bold\">\s*.+>(.+)</a>#", $data, $date))
				return $date[1];	
		}
	}

   	public function parseDirector(&$data)
     {  
	  	if ($data!=false)
	  	{
	       	if (preg_match("#Réalisé par (.+)</span>#", $data, $director))
				return strip_tags($director[1]);	
		}
     }

     public function parseActor(&$data)
     {
	  	if ($data!=false)
	  	{
	       	if (preg_match("#Avec (.+)</a>#", $data, $actors))
				return strip_tags($actors[1]);
		}
	}
     
     public function parseOrigin(&$data)
     {
	  	if ($data!=false)
	  	{
	       	if (preg_match("#Long-métrage\s*(.+)</a>#", $data, $origin))
				return strip_tags($origin[1]);
		}
	}

     public function parseType(&$data)
     {
	  	if ($data!=false)
	  	{
	       	if (preg_match("#Genre : \s*(.+)</a>#", $data, $type))
				return strip_tags($type[1]);
		}
	}
     
     public function parseSypnosis(&$data)
     {
	  	if ($data!=false)
	  	{
	       	if (preg_match("#Synopsis : (.+)</p>#", $data, $synopsis))
				return strip_tags($synopsis[1]);
		}
	}

     public function parseTitle(&$data)
     {
	  	if ($data!=false)
	  	{
	       	if (preg_match("#<span title.+>(.+)</span>#", $data, $title))
				return $title[1];
		}
	}

     public function parsePoster(&$data)
     {
	  	if ($data!=false)
	  	{
	       	if (preg_match("#<link rel=\"image_src\" href=\"(.+)\" />#", $data, $poster))
				return $poster[1];
		}
	}

}

//---TEST---//
/*---Look for a movie in the database Allocine---*/
/*$database = new AllocineDatabase();
$moviesArray = $database->seekMovie("scream");
print_r($moviesArray);*/

/*---Get informations about a movie---*/
/*$movie = new AllocineMovie();
$movieData = $movie->getHtmlData("http://www.allocine.fr/film/fichefilm_gen_cfilm=133046.html");
echo $movie->parseReleaseDate($movieData)."<br/>";
echo $movie->parseDirector($movieData)."<br/>";
echo $movie->parseActor($movieData)."<br/>";
echo $movie->parseOrigin($movieData)."<br/>";
echo $movie->parseTitle($movieData)."<br/>";
echo $movie->parseType($movieData)."<br/>";
echo $movie->parseSypnosis($movieData)."<br/>";
echo '<img src="'.$movie->parsePoster($movieData).'" alt="poster"/><br/>';
*/
?>



