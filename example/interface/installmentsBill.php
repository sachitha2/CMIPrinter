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

	$Wid = 7;
	$Winstallment = 16;
	$Wddate = 18;
	$Wrpay = 23;
	$Wrdate = 18;
	
	$printer -> setPrintLeftMargin(0);
	$printer -> text("     ID     INSTALLMENT          DUE DATE\n");
	$printer -> text("       RECEIVED PAYMENT     RECEIVED DATE\n\n");
	//$printer -> text("0ABBAABBAABBAABBAABBAABBAABBAABBAABBAABBAABBAABB\n");

		 $dataArrLength = sizeof($phpArr['data']['id']);
		 echo($dataArrLength);

    for($x = 0;$x < $dataArrLength;$x++){
		
		$id = $phpArr['data']['id'][$x];
		$installment = $phpArr['data']['installment'][$x];
		$rPayment = $phpArr['data']['rPayment'][$x];
		$dueDate = $phpArr['data']['dueDate'][$x];
		$rDate = $phpArr['data']['rDate'][$x];

		$String1 = "";
		$String2 = "";

		for($y = 1 ; $y <= ($Wid-strlen($id)) ; $y++){
			$String1 = $String1." ";
		}
		$String1 = $String1.$id;

		for($y = 1 ; $y <= ($Winstallment-strlen($installment)) ; $y++){
			$String1 = $String1." ";
		}
		$String1 = $String1.$installment;

		for($y = 1 ; $y <= ($Wddate-strlen($dueDate)) ; $y++){
			$String1 = $String1." ";
		}
		$String1 = $String1.$dueDate;

		for($y = 1 ; $y <= ($Wrpay-strlen($rPayment)) ; $y++){
			$String2 = $String2." ";
		}
		$String2 = $String2.$rPayment;

		for($y = 1 ; $y <= ($Wrdate-strlen($rDate)) ; $y++){
			$String2 = $String2." ";
		}
		$String2 = $String2.$rDate;

		$printer -> text("$String1\n");
		$printer -> text("$String2\n\n");
		//$printer -> text("ID  ITEM      QTY      PRICE      TOTAL    R\n");
		////here //TODO
		//$printer -> text("              $qty     $price       $total     $r\n");


		// for($x = 1 ; $x <= ($Wprice-strlen($price)) ; $x++){
		// 	$String += "";
		// }

		//$printer -> text("          $qty     $price       $total     $r\n");
	}
	
	$SinsTot = "  Installment Total :";
	$SrTot = "  Recieved Total    :";
	$StoPay = "  To be paid        :";

	$insTot=$phpArr['data']['mainData']['insTot'];
	$rTot=$phpArr['data']['mainData']['rAmount'];
	$toPay=$phpArr['data']['mainData']['dueAmount'];

	for($z=0 ; $z <= (24-strlen($insTot)) ; $z++){
		$SinsTot = $SinsTot." ";
	}
	$SinsTot = $SinsTot.$insTot;

	for($z=0 ; $z <= (24-strlen($rTot)) ; $z++){
		$SrTot = $SrTot." ";
	}
	$SrTot = $SrTot.$rTot;

	for($z=0 ; $z <= (24-strlen($toPay)) ; $z++){
		$StoPay = $StoPay." ";
	}
	$StoPay = $StoPay.$toPay;

	$printer -> text("\n\n  --------------------------------------------\n");
	$printer -> text("$SinsTot\n");
	$printer -> text("$SrTot\n");
	$printer -> text("$StoPay\n");

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

		//TODO
		// $arr['data']['id'][0] = 10;
		// $arr['data']['id'][1] = 10;
		
		// $arr['data']['item'][0] = "Soap";
		// $arr['data']['item'][1] = "valnila";
		
		// $arr['data']['QTY'][0] = 100;
		// $arr['data']['QTY'][1] = 250;
		
		// print_r($arr);
		// echo("<br>");
		// $json = json_encode($arr);
		// echo($json);
		
		
		
		// echo("Data Stream not found");
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

