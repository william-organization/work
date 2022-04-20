<?php
include('gateway.php');
$CSGW = new P3\SDK\Gateway;
$key = '9GXwHNVC87VqsqNM'; // Should be $merchantSecret from the file gateway.php -> change if needed


// Request
$req = array(
    'merchantID' => '119837', // Should be $merchantID from the file gateway.php -> change if needed
    'action' => 'SALE',
    'type' => 1,
    'countryCode' => 826,
    'currencyCode' => 826,
    'amount' => $_POST['Amount'],
    'cardNumber' => $_POST['CardNumber'],
    'cardExpiryMonth' => 12,
    'cardExpiryYear' => 21,
    'cardCVV' => $_POST['CVV'],
    'customerName' => 'Test Customer',
    'customerEmail' => 'test@testcustomer.com',
    'customerAddress' => '16 Test Street',
    'customerPostCode' => 'TE15 5ST',
    'orderRef' => 'Test purchase - ' .uniqid(),
    'transactionUnique' => (isset($_REQUEST['transactionUnique']) ?
    $_REQUEST['transactionUnique'] : uniqid()),
    'threeDSMD' => (isset($_REQUEST['MD']) ? $_REQUEST['MD'] : null),
    'threeDSPaRes' => (isset($_REQUEST['PaRes']) ? $_REQUEST['PaRes'] : null),
    'threeDSPaReq' => (isset($_REQUEST['PaReq']) ? $_REQUEST['PaReq'] : null)
    );

$res = $CSGW->directRequest($req);

// Check the response code
if ($res['responseCode'] == 65802) {
// Send details to 3D Secure ACS and the return here to repeat request
$pageUrl =  'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
echo "
<p>Your transaction requires 3D Secure Authentication</p>
<form action=\"" . htmlentities($res['threeDSACSURL']) . "\"method=\"post\">
<input type=\"hidden\" name=\"MD\" value=\"" . htmlentities($res['threeDSMD']) . "\">
<input type=\"hidden\" name=\"PaReq\" value=\"" . htmlentities($res['threeDSPaReq']) .
"\">
<input type=\"hidden\" name=\"TermUrl\" value=\"" . htmlentities($pageUrl) . "\">
<input type=\"submit\" value=\"Continue\">
</form>
";
} else if ($res['responseCode'] === 0) {
echo "<p>Thank you for your payment.". htmlentities($res['responseMessage']). "</p>";
} else {
echo "<p>Failed to take payment: " . htmlentities($res['responseMessage']) .
"</p>";
}


echo('<br><hr><br><h3>var_dump of $res object</h3>');

var_dump($res);
