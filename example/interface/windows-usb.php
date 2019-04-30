<?php
/* Change to the correct path if you copy this example! */
require __DIR__ . '/../../autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

date_default_timezone_set("Asia/Kolkata");

/**
 * Install the printer using USB printing support, and the "Generic / Text Only" driver,
 * then share it (you can use a firewall so that it can only be seen locally).
 *
 * Use a WindowsPrintConnector with the share name to print.
 *
 * Troubleshooting: Fire up a command prompt, and ensure that (if your printer is shared as
 * "Receipt Printer), the following commands work:
 *
 *  echo "Hello World" > testfile
 *  copy testfile "\\%COMPUTERNAME%\Receipt Printer"
 *  del testfile
 */
try {
	if(isset($_GET['data'])){
		$getData = $_GET['data'];
		$phpArr = json_decode($getData,true);
		print_r($phpArr);
    // Enter the share name for your USB printer here
    // $connector = null;
    $connector = new WindowsPrintConnector("sam");

    /* Print a "Hello world" receipt" */
    $printer = new Printer($connector);
    $printer->setTextSize(3, 3);
    // $img = EscposImage::load("d.png");
    // $printer -> graphics($img);
	$printer->setJustification(Printer::JUSTIFY_CENTER);
	
    $printer -> text("CMI Pvt Ltd\n\n");
	$printer->setJustification(Printer::JUSTIFY_LEFT);
	$printer->setTextSize(1, 1);

	$Wid = 4;
	$Witem = 6;
	$Wqty = 8;
	$Wprice = 9;
	$Wtotal = 9;
	$Wr = 8;
	
	$printer -> setPrintLeftMargin(0);
	$printer -> text("  ID  ITEM    QTY    PRICE    TOTAL        R\n");
	//$printer -> text("AABBAABBAABBAABBAABBAABBAABBAABBAABBAABBAABBAABB\n");
		$dataArrLength = sizeof($phpArr['data']['id']);
		echo($dataArrLength);
    for($x = 0;$x < $dataArrLength;$x++){
		
		$id = "";
		$dataId = $phpArr['data']['id'][$x];
		$dataItem = $phpArr['data']['item'][$x];
		//meka balanna
		if($x<10){
			$id .=" $dataId";
		}else{
			$id="$dataId";
		}
		
		$qty = $phpArr['data']['QTY'][$x];
		$price = $phpArr['data']['price'][$x];
		$total = $phpArr['data']['total'][$x];
		$r = $phpArr['data']['r'][$x];

		if($id < 10){
			$id = "  $id";
		}elseif($id < 100){
			$id = " $id";
		}

		$printer -> text("$id  $dataItem\n");
		//$printer -> text("ID  ITEM      QTY      PRICE      TOTAL    R\n");
		////here //TODO
		//$printer -> text("              $qty     $price       $total     $r\n");

		$String = "         ";
		for($y = 1 ; $y <= ($Wqty-strlen($qty)) ; $y++){
			$String = $String." ";
		}
		$String = $String."$qty";

		
		for($y = 1 ; $y <= ($Wprice-strlen($price)) ; $y++){
			$String = $String." ";
		}
		$String = $String."$price";

		
		for($y = 1 ; $y <= ($Wtotal-strlen($total)) ; $y++){
			$String = $String." ";
		}
		$String = $String."$total";

		for($y = 1 ; $y <= ($Wprice-strlen($r)) ; $y++){
				$String = $String." ";
			}
		$String = $String."$r";

		$printer -> text("$String\n");

		// for($x = 1 ; $x <= ($Wprice-strlen($price)) ; $x++){
		// 	$String += "";
		// }

		//$printer -> text("          $qty     $price       $total     $r\n");
	}
	
	$printer -> setPrintLeftMargin(0);
	
	$printer->setJustification(Printer::JUSTIFY_LEFT);
	$printer->setTextSize(1, 1);
	$Stot="  Total   :";
	$Scash="  CASH    :";
	$Sbal="  Balance :";

	$tot=$phpArr['data']['mainData']['total'];
	$cash=$phpArr['data']['mainData']['cash'];
	$bal=$phpArr['data']['mainData']['balance'];

	for($z=0 ; $z <= (34-strlen($tot)) ; $z++){
		$Stot = $Stot." ";
	}
	$Stot = $Stot.$tot;

	for($z=0 ; $z <= (34-strlen($cash)) ; $z++){
		$Scash = $Scash." ";
	}
	$Scash = $Scash.$cash;

	for($z=0 ; $z <= (34-strlen($bal)) ; $z++){
		$Sbal = $Sbal." ";
	}
	$Sbal = $Sbal.$bal;

	$printer -> text("\n\n\n$Stot\n");
	$printer -> text("$Scash\n");
	$printer -> text("$Sbal\n");

	$printer->setJustification(Printer::JUSTIFY_CENTER);
	$printer->setTextSize(2, 2);
	$printer -> text("\n\nThank You!\n\n");
	$printer->setTextSize(1, 1);
	$date = date("M,d,Y h:i:s");
	$printer -> text("$date\n\n");
	$printer->text("http://infinisolutionslk.com\n");
	$printer->text("077-1466460\n");
    $printer -> cut();
    
    /* Close printer */
    $printer -> close();
	}else{
		$arr['data']['id'][0] = 10;
		$arr['data']['id'][1] = 10;
		
		$arr['data']['item'][0] = "Soap";
		$arr['data']['item'][1] = "valnila";
		
		$arr['data']['QTY'][0] = 100;
		$arr['data']['QTY'][1] = 250;
		
		print_r($arr);
		echo("<br>");
		$json = json_encode($arr);
		echo($json);
		
		
		
		echo("Data Stream not found");
	}
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}

function compactCharTable($printer, $start = 4, $header = false)
{
    /* Output a compact character table for the current encoding */
    $chars = str_repeat(' ', 256);
    for ($i = 0; $i < 255; $i++) {
        $chars[$i] = ($i > 32 && $i != 127) ? chr($i) : ' ';
    }
    if ($header) {
        $printer -> setEmphasis(true);
        $printer -> textRaw("  0123456789ABCDEF0123456789ABCDEF\n");
        $printer -> setEmphasis(false);
    }
    for ($y = $start; $y < 8; $y++) {
        $printer -> setEmphasis(true);
        $printer -> textRaw(strtoupper(dechex($y * 2)) . " ");
        $printer -> setEmphasis(false);
        $printer -> textRaw(substr($chars, $y * 32, 32) . "\n");
    }
}

