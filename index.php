<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
     <head>
	  <title>TEST</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     </head>

     <body>
	  
	  <form method=post action="index.php">
	       <input type="text" name="search"/>
	       <input type="submit" value="Search"/>
	  </form>

<?php
     include_once("class.php");
     
     if (isset($_POST['search']))
     {
	  $name = htmlspecialchars($_POST['search']);
	  $movie = new AllocineMovie();
	  $data = $movie->seekFromAllocine($name);
	  $listMovie = $movie->getMovieList($data);
	  
	  if ($listMovie!=false)
	  {
?>
	       <p>
		    <form method=post action="index.php">
<?php
		    $nb=0;
		    foreach ($listMovie as $film)
		    {
?>		    
			 <input type="radio" name="info" value="<?php echo $film['url'];?>" id="mov<?php echo $nb;?>" /><label for="mov<?php echo $nb;?>" > <?php echo $film['title'];?> </label>
			 <br/>
<?php
			 $nb++;
			 }
?>
			 <input type="hidden" name="search" value="<?php echo $name;?>" />
			 <input type="submit" value="Details"/>
		    </form>
	       </p>
	       <p>
<?php
		    if (isset($_POST['info']))
		    {
			 $url = htmlspecialchars($_POST['info']);
			 $data = $movie->findInfoFromAllocine($url);
			 echo "<img src='".$movie->getPoster($data)."'/><br/>";
			 echo $movie->getTitle($data)."<br/>";
			 echo $movie->getDate($data)."<br/>";
			 echo $movie->getDirector($data)."<br/>";
			 echo $movie->getActor($data)."<br/>";
			 echo $movie->getOrigin($data)."<br/>";
			 echo $movie->getType($data)."<br/>";
			 echo $movie->getSypnosis($data)."<br/>";
		    }
?>
	       </p>
<?php
	  }
	  else
	       echo "No movie has been found in Allocine";
     }
?>

     </body>
</html>
