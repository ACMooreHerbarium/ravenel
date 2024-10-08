<?php
	/** 
	 *@file
	 * fungi.php
	 */

	require_once("includes/configuration.php");

	$application->setTitle("Fungi Caroliniani Exsiccati");

	$pointers = array(9705, 9232, 9307, 9461, 9540, 8128);
	$ptrQueryStrs = array();
	foreach($pointers as $ptr)
	{
		$ptrQueryStrs[] = "pointer = '$ptr'";
	}

	$query = "SELECT parent_object, pointer, title FROM manuscripts";
	$query .= " WHERE (".implode(" OR ",$ptrQueryStrs).")";
	$query .= " ORDER BY pointer ASC";

	// http://digital.tcl.sc.edu/cdm/search/collection/rav/searchterm/Fungi%20Caroliniana%20Exsicatti,%20Century/field/title/mode/any/conn/and/order/nosort/ad/asc/cosuppress/1

	$errorMessage = "";
	try
	{
		//$dbh = new PDO($dsn, $user, $password);
		$myPDO = new PDO('pgsql:host=107.170.104.93;port=5432;dbname=ravenel', 'cdhconnector','!HWRPl@nt$');
	}
	catch(PDOException $e)
	{
		$errorMessage .= "Connection failed: ".$e->getMessage();
		LogManager::LogError("On fungi.php: ".$errorMessage);
	}
	//$myPDO = new PDO('pgsql:host=107.170.104.93;port=5432;dbname=ravenel', 'cdhconnector');
	//$myPDO = new PDO('pgsql:host=107.170.104.93;port=5432;dbname=ravenel', 'cdhconnector', '!HWRPl@nt$');

	//$DBConnection = pg_connect('host=107.170.104.93 port=5432 dbname=ravenel user=cdhconnector password=!HWRPl@nt$');

	//$data = pg_query($DBConnection,$query);

	//$results = pg_fetch_all($data);

	require("layout/header.php");

?>
<main class="container">
	<div class="row page-header">
		<div class="col-xs-12">
			<h1>Fungi of Carolina</h1>
			<p class="lead">Published by Ravenel in 5 sets (Centuries) and numbered 1-100 each between the years of 1852-1860.<br/>Illustrated By Natural Specimens of the Species</p>
		</div>
	</div>
<?php
	if($errorMessage == "")
	{
		$prepare = $myPDO->prepare($query);
		$prepare->execute();
		$results = $prepare->fetchAll(PDO::FETCH_ASSOC);
?>
	<div class="row">
<?php
		if(is_array($results) && count($results) > 0)
		{
			$count = 0;
			foreach($results as $key => $result)
			{
				$info = GetJSONDataFromLink($application->getManuscriptCompoundObjectInfo($result['pointer']),true);
				if(isset($info['page'][0]['pageptr'])) // this should always work but just in case something breaks
				{
					$first = $info['page'][0]['pageptr'];

					$query = "SELECT image_height, image_width FROM manuscripts WHERE pointer = :pointer";
					
					$prepare = $myPDO->prepare($query);
					$prepare->bindParam(':pointer', $first);
					$prepare->execute();

					$image = $prepare->fetchObject();
?>
		<a href="<?php print ROOT_FOLDER; ?>viewer.php?type=transcript&institute=Carolina&number=<?=$first?>">
			<div class="col-sm-4">
				<h2><?=$result["title"]?></h2>

				<img src="<?=$application->getManuscriptImage($first, $image->image_width, $image->image_height)?>" alt="<?=$result["title"]?>" class="img-responsive">
			</div>
		</a>
<?php
					$count++;
					if($count % 3 == 0)
					{
?>
	</div>
	<hr>
	<div class="row">
<?php
					}
				}
				else
				{
					LogManager::LogError("On fungi.php, result (printed below) failed to pull back data from json link \r\n".print_r($result,true));
				}
			}
		}
		else
		{
			LogManager::LogError("On fungi.php, the query failed to pull back data","",$query);
		}
	}
	else
	{
		//bad things happened, error message already logged
	}
?>
	</div>
</main>
<?php
	require("layout/footer.php");
?>
