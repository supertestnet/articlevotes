# 1. Generate lnbits wallet

## How to call generateLNBitsWallet

Before using this function your lnbits instance must have the user manager extension installed and one user created. Once that is done, your user_id can be found in the Users section of the User Manager extension page and your admin_id can be found in the url of the User Manager extension page after the parameter “usr=”.

	<?php

	include_once('LightningController.php');

	$lnbits_url = "https://lnbits.com";
	$user_id = "6447d24778514d21a746aa9c38cd4951";
	$wallet_name = "testwallet";
	$admin_id = "f54c3d4265ed4585afc86162ddf4a38c";
	$lnbits_apikey = "56922bd5e5524331a53766e04181d73d";

	?>

	<html>
	<body>

	<?php

	$LightningController = new App\Http\Controllers\LightningController();
	$newWallet = $LightningController->generateLNBitsWallet( $lnbits_url, $user_id, $wallet_name, $admin_id, $lnbits_apikey );
	echo $newWallet;

	?>

	</body>
	</html>

## What to expect as a result of running generateLNBitsWallet

	{ "admin": "f54c3d4265ed4585afc86162ddf4a38c", "adminkey": "d9327824af6c42dc8a9acc7515a7ed3f", "id": "9c05fe586bb44c638066c6ecc9defd82", "inkey": "3905112ba35d49929fc0daff4beb2a4a", "name": "test2wallet2", "user": "6447d24778514d21a746aa9c38cd4951" }

Also note that one user can have many wallets so you can have a user called something like “Lightning Video Game” and then each player can have a unique wallet belonging to that one user. Each player does not need to be a different lnbits “user,” they each just need a different lnbits wallet.

# 2. Request invoice

## How to call requestInvoice

	<?php

    include_once('LightningController.php');

    $lnbits_url = "https://lnbits.com";
    $amount = 10;
    $memo = "Test Invoice";
    $lnbits_apikey = "56922bd5e5524331a53766e04181d73d";
    $webhook = "http://example.com/test.php?test=true&test2=false";

    ?>

    <html>
    <body>

    <?php

    $LightningController = new App\Http\Controllers\LightningController();
    $invoice = $LightningController->requestInvoice( $lnbits_url, $amount, $memo, $lnbits_apikey, $webhook );
    echo $invoice;

    ?>

    </body>
    </html>

## What to expect as a result of running requestInvoice

	{"invoice":"lnbc1u1p0afau8pp5rmpk82za3x22uvnhaqahtuerls7nfg846hkka55sz6qg9s0rfs3sdq523jhxapqf9h8vmmfvdjscqzpgxqyz5vqsp56he3s5j8nx5ysc89cppuguzxsyd3jngsf6yx0yf3c0g4d7ecwr6q9qy9qsqfvscrhk28hskapg2xqy2r5udmyh5jk8knpk3n0yu9jar6mu4swtzgxwusrwr62qnpcv48lctsvm84hfz53gj2au3nxqw9hy34zvknzgqkvlxrs","pmthash":"1ec363a85d8994ae3277e83b75f323fc3d34a0f5d5ed6ed290168082c1e34c23"}
    
## What happens when the invoice is paid

When the invoice is paid, a get request will be sent to http://example.com/test.php with the parameters "test=true" and "test2=false." A script there can then (for example) write those parameters to a file.

# 3. Check invoice status

## How to call checkInvoice

	<?php

	include_once('LightningController.php');

	$lnbits_url = "https://lnbits.com";
	$pmthash = "3b3da8aa1afc15b49e29e488d93eb8a54628ccd2101e74e99e3585903006bee3";
	$lnbits_apikey = "56922bd5e5524331a53766e04181d73d";

	?>

	<html>
	<body>

	<?php

	$LightningController = new App\Http\Controllers\LightningController();
	$status = $LightningController->checkInvoice( $lnbits_url, $pmthash, $lnbits_apikey );
	echo $status;

	?>

	</body>
	</html>

## What to expect as a result of running checkInvoice

	0 [if unpaid]
or
	1 [if paid]

# 4. Pay invoice

## How to call payInvoice

	<?php

	include_once('LightningController.php');

	$lnbits_url = "https://lnbits.com";
	$vg995krgqqqqlgqqqqqeqqjq7qhxn0p7ky664ndn2jxn5s5r5rdqteprgcfjmep4njdk95rm79wztz9ulvmmwp3sp55gcadj8nev8zdrx2h0d4zx4vpvd4rl2q49q5gp84dql4";
	$lnbits_apikey = "56922bd5e5524331a53766e04181d73d";

	?>

	<html>
	<body>

	<?php

	$LightningController = new App\Http\Controllers\LightningController();
	$status = $LightningController->payInvoice( $lnbits_url, $invoice, $lnbits_apikey );
	echo $status;

	?>

	</body>
	</html>

## What to expect as a result of running payInvoice

	1 [if successful]
or
	{"error":"Insufficient balance."}
or
	{"error":"Bad bech32 checksum"}

# 5. Delete lnbits wallet

## How to call generateLNBitsWallet

	<?php

	include_once('LightningController.php');

	$lnbits_url = "https://lnbits.com";
	$wallet_id = "d4076bb438f2463380639490aa55fb04";
	$lnbits_apikey = "56922bd5e5524331a53766e04181d73d";

	?>

	<html>
	<body>

	<?php

	$LightningController = new App\Http\Controllers\LightningController();
	$status = $LightningController->deleteLNBitsWallet( $lnbits_url, $wallet_id, $lnbits_apikey );
	echo $status;

	?>

	</body>
	</html>

## What to expect as a result of running generateLNBitsWallet

	Nothing -- this function does not return anything
