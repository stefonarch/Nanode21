<?php
// include config and functions
require_once($_SERVER["DOCUMENT_ROOT"] . '/modules/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/modules/functions.php');
?>

<!DOCTYPE html>

<head>
<link rel="stylesheet" type="text/css" href="modules/style.css">
<title>Nano Node 21</title>
<meta http-equiv="refresh" content="<?php echo $autoRefreshInSeconds; ?>">
</head>

<body>
<?php

// get curl handle
$ch = curl_init();

if (!$ch)
{
  myError('Could not initialize curl!');
}

// we have a valid curl handle here
// set some curl options
curl_setopt($ch, CURLOPT_URL, 'http://'.$nanoNodeRPCIP.':'.$nanoNodeRPCPort);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// -- Get Version String from nano_node ---------------
$rpcVersion = getVersion($ch);
$version = $rpcVersion->{'node_vendor'};

// -- Get get current block from nano_node 
$rpcBlockCount = getBlockCount($ch);
$currentBlock = $rpcBlockCount->{'count'};
$uncheckedBlocks = $rpcBlockCount->{'unchecked'};

// -- Get number of peers from nano_node 
$rpcPeers = getPeers($ch);
$peers = (array) $rpcPeers->{'peers'};
$numPeers = count($peers);

// -- Get node account balance from nano_node 
$rpcNodeAccountBalance = getAccountBalance($ch, $nanoNodeAccount);
$accBalanceMnano = rawToMnano($rpcNodeAccountBalance->{'balance'},4);
$accPendingMnano = rawToMnano($rpcNodeAccountBalance->{'pending'},4);

// -- Get representative info for current node from nano_node 
$rpcNodeRepInfo = getRepresentativeInfo($ch, $nanoNodeAccount);
$votingWeight = rawToMnano($rpcNodeRepInfo->{'weight'},4);
$repAccount = $rpcNodeRepInfo->{'representative'};


// close curl handle
curl_close($ch);
?>




<!-- Nano Market Data Section-->

<a href="https://nano.org/" target="_blank">
	<img src="modules/nano-logo.png" class="logo" alt="Logo Nano"/>
</a>
<h1> Nano Node 21 running</h1>
<br style="clear:all">

<?php

// get nano data from coinmarketcap
$nanoCMCData = getNanoInfoFromCMCTicker($cmcTickerUrl);


if (!empty($nanoCMCData))
{ // begin nano market data section

  // beautify market info to be displayed
  $nanoMarketCapUSD = "$" . number_format( (float) $nanoCMCData->{'market_cap_usd'} / pow(10,9), 2 ) . "B";
  $nanoMarketCapEUR =       number_format( (float) $nanoCMCData->{'market_cap_eur'} / pow(10,9), 2 ) . "Mâ‚¬";

  $nanoPriceUSD = "$" . number_format( (float) $nanoCMCData->{'price_usd'} , 2 );
  $nanoPriceEUR =       number_format( (float) $nanoCMCData->{'price_eur'} , 2 ) . "â‚¬";

  $nanoChange24hPercent = number_format( (float) $nanoCMCData->{'percent_change_24h'}, 2 );
  $nanoChange7dPercent  = number_format( (float) $nanoCMCData->{'percent_change_7d'}, 2 );


  // color values for positive and negative change
  $colorPos = "darkgreen";
  $colorNeg = "firebrick";

  $nanoChange24hPercentHTMLCol = $colorNeg;
  $nanoChange7dPercentHTMLCol  = $colorNeg;


  // prepend '+' sign and make it green (hopefully ...)
  if ( $nanoChange24hPercent > 0)
  {
    $nanoChange24hPercent  = "+" . $nanoChange24hPercent;
    $nanoChange24hPercentHTMLCol = $colorPos;
  }

  if ( $nanoChange7dPercent > 0)
  {
    $nanoChange7dPercent  = "+" . $nanoChange7dPercent;
    $nanoChange7dPercentHTMLCol = $colorPos;
  }

  // append '%''
  $nanoChange24hPercent = $nanoChange24hPercent . "%";
  $nanoChange7dPercent  = $nanoChange7dPercent . "%";

?>

<!-- Nano Market Data Table -->

<div class="ticker">
Value: <?php print ($nanoPriceUSD . " | " . $nanoPriceEUR . " | " . $nanoPriceBTC); ?>  <?php print ("<span style='color:" . $nanoChange24hPercentHTMLCol . "'>" . $nanoChange24hPercent . " (24h)</span> | ". "<span style='color:" . $nanoChange7dPercentHTMLCol  . "'>" . $nanoChange7dPercent .  " (7d)</span>"); ?>

<?php
}
?>
</div>

<!-- Node Info Table -->

<div class="info">	
<0p class="medium">Nanode21 is running at the moment two nodes for the Nano currency network, <a href="http://172.104.246.18/display.php" target:"_blank">one</a> at Frankfurt, Germany and this one here in Tokyo, Japan. <br/>
Running nodes helps keeping the network in a good shape and - if selected as <a href="https://www.nanode.co/representatives" target:"_blank">representative nodes</a> - make sure that the  voting process is as decentralized as possible.<br/>
If you are interested in running your own node: <a href="setupnode.htm">here</a>'s a simple howto for it.<br/>
Otherwise feel free to select this node as a representative in your wallet. Donation address at the bottom.</p>


<h3>Node Information:</h3>

<p class="medium">Version: 10.0.1<br/>
Current Block: <?php print($currentBlock) ?><br/>
Number of Unchecked Blocks: <?php print($uncheckedBlocks) ?><br/>
Number of Peers: <?php print($numPeers) ?><br/>
Address: <a  href="https://www.nanode.co/account/<?php print($nanoNodeAccount); ?>" target="_blank">xrb_1i9ugg14c5sp....59xn45mwoa54</a><br/>
Voting Weight: <?php echo $votingWeight; ?> Nano<br/<br/>
Systems: Arch Linux: Dual Core 4gb Ram<br/>
&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Ubuntu 16.4.3: 1Gb ram single core<br/>
System Load Average: <?php print(getSystemLoadAvg()); ?><br/>
<?php
  $data = shell_exec('uptime');
  $uptime = explode(' up ', $data);
  $uptime = explode(',', $uptime[1]);
  $uptime = $uptime[0].', '.$uptime[1];

  echo ('Current server uptime: '.$uptime.'
');

?>
</p>

</div>

<!-- Footer -->

<hr>

<p class="small"> This  here is a fork of <a href="https://github.com/dbachm123/phpNodeXRai" target="_blank">phpNodeXrai</a></p>
<p class="small">Server Cost: $20/mo.  Donations: @nanode21  
<a  href="https://www.nanode.co/account/<?php print($nanoDonationAccount); ?>" target="_blank"><?php print($nanoDonationAccount); ?></a>
</p>

</body>
</html>