<?php

/*
class WebPage : It enables us to communicate with a http web servor.
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

/*
class AllocineMovie : It enables us to use the web search engine of Allocine to obtain details about movies.
*/
class AllocineMovie
{
     public function seekFromAllocine($movieName)
     {
	  $name = urlencode($movieName);
	  $url = "http://www.allocine.fr/recherche/?q=".$name;
	  $page = new WebPage($url);
	  $data = $page->getHtmlData();
	  
	  return $data;
     }

     public function getMovieList(&$data)
     {
	  //---parsing of $data---
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "dans les titres de films");
	       if (!($pos1===false))
	       {
		    $pos2 = strpos($data,"</table>",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 //echo $data_brut."<br/>";
		    
			 $pos_ligne_1 = strpos($data_brut, "<tr><td");
			 $pos_ligne_2 = strpos($data_brut, "</td></tr>", $pos_ligne_1);

			 $movie = array(); //Save title and url

			 while ($pos_ligne_1 & $pos_ligne_2)
			 {
			      //echo $pos_ligne_1.":".$pos_ligne_2."<br/>";
			      $ligne = substr($data_brut, $pos_ligne_1, $pos_ligne_2-$pos_ligne_1);
			      //echo htmlentities($ligne)."<br/>";
			      $title = trim(strip_tags($ligne));
			      //echo $title."<br/>";

			      $pos_url_1 = strpos($ligne, "<a");
			      $pos_url_2 = strpos($ligne, ">", $pos_url_1);
			      //echo $pos_url_1.":".$pos_url_2."<br/>";
			      $link = substr($ligne, $pos_url_1+9, $pos_url_2-$pos_url_1-10); //9 and 10 t remove <a href=' and '
			      $url = "http://www.allocine.fr".$link;
			      //echo $url."<br/>";

			      $movie[]=array('title' => $title, 'url' => $url);

			      $pos_ligne_1 = strpos($data_brut, "<tr><td",$pos_ligne_2);
			      $pos_ligne_2 = strpos($data_brut, "</td></tr>", $pos_ligne_1);
			 }
		    
			 return $movie;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }

     public function findInfoFromAllocine($url)
     {
	  $page = new WebPage($url);
	  $data = $page->getHtmlData();
	  
	  return $data;
     }
     
     public function getDate(&$data)
     {
	  //---parsing of $data---
	  
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "Date de sortie");
	       if (!($pos1===FALSE))
	       {
		    $pos2 = strpos($data,"</span>",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 $date = trim(strip_tags($data_brut));
			 
			 return $date;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }

     public function getDirector(&$data)
     {  
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "Réalisé par");
	       if (!($pos1===FALSE))
	       {
		    $pos2 = strpos($data,"</span>",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 $director = trim(strip_tags($data_brut));
			 
			 return $director;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }

     public function getActor(&$data)
     {
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "Avec");
	       if (!($pos1===FALSE))
	       {
		    $pos2 = strpos($data,"plus",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 $actor = trim(strip_tags($data_brut));
			 
			 return $actor;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }  
     
     public function getOrigin(&$data)
     {
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "Long-métrage");
	       if (!($pos1===FALSE))
	       {
		    $pos2 = strpos($data,"</a>",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 $origin = trim(strip_tags($data_brut));
			 
			 return $origin;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }

     public function getType(&$data)
     {
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "Genre");
	       if (!($pos1===FALSE))
	       {
		    $pos2 = strpos($data,"</a>",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 $type = trim(strip_tags($data_brut));
			 
			 return $type;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }
     
     public function getSypnosis(&$data)
     {
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "Synopsis");
	       if (!($pos1===FALSE))
	       {
		    $pos2 = strpos($data,"</p>",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 $syp = trim(strip_tags($data_brut));
			 
			 return $syp;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }

     public function getTitle(&$data)
     {
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "<h1>");
	       if (!($pos1===FALSE))
	       {
		    $pos2 = strpos($data,"</h1>",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 $title = trim(strip_tags($data_brut));
			 
			 return $title;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }  

     public function getPoster(&$data)
     {
	  if ($data!=false)
	  {
	       $pos1 = strpos($data, "imagecontainer");
	       if (!($pos1===FALSE))
	       {
		    $pos2 = strpos($data,"alt",$pos1);
		    if (!($pos2===FALSE)) 
		    {
			 //echo $pos1.":".$pos2."<br/>";
			 $data_brut = substr($data,$pos1,$pos2-$pos1);
			 $pos_url_1 = strpos($data_brut, "http");
			 $pos_url_2 = strpos($data_brut, "'",$pos_url_1);
			 $urlPoster = substr($data_brut,$pos_url_1,$pos_url_2-$pos_url_1);
			 			 
			 return $urlPoster;
		    }
		    else
			 return false;
	       }
	       else
		    return false;
	  }
	  else
	       return false;
     }  

}

//---TEST---//

//$movie = new AllocineMovie();
/*$data = $movie->seekFromAllocine("shrek");
$listMovie = $movie->getMovieList($data);
print_r($listMovie);*/

/*$data = $movie->findInfoFromAllocine("http://www.allocine.fr/film/fichefilm_gen_cfilm=7651.html");
echo "<img src='".$movie->getPoster($data)."'/><br/>";
echo $movie->getTitle($data)."<br/>";
echo $movie->getDate($data)."<br/>";
echo $movie->getDirector($data)."<br/>";
echo $movie->getActor($data)."<br/>";
echo $movie->getOrigin($data)."<br/>";
echo $movie->getType($data)."<br/>";
echo $movie->getSypnosis($data)."<br/>";*/

?>



