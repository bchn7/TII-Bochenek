<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();

if(!empty($_GET["action"])) {
    switch($_GET["action"]) {
        case "add":
            if(!empty($_POST["quantity"])) {
                $productByCode = $db_handle->runQuery("SELECT * FROM tblproduct WHERE code='" . $_GET["code"] . "'");
                $itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price"], 'image'=>$productByCode[0]["image"]));
                
                if(!empty($_SESSION["cart_item"])) {
                    if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
                        foreach($_SESSION["cart_item"] as $k => $v) {
                                if($productByCode[0]["code"] == $k) {
                                    if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                        $_SESSION["cart_item"][$k]["quantity"] = 0;
                                    }
                                    $_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
                                }
                        }
                    } else {
                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
                    }
                } else {
                    $_SESSION["cart_item"] = $itemArray;
                }
            }
        break;
        case "remove":
            if(!empty($_SESSION["cart_item"])) {
                foreach($_SESSION["cart_item"] as $k => $v) {
                        if($_GET["code"] == $k)
                            unset($_SESSION["cart_item"][$k]);				
                        if(empty($_SESSION["cart_item"]))
                            unset($_SESSION["cart_item"]);
                }
            }
        break;
        case "empty":
            unset($_SESSION["cart_item"]);
        break;	
    }
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <title>Tutaj bedzie strona</title>
</head>
<body>
    <div class='container'> 
        
        <div class="header">
            
            <div class="logo"> 
            <p>Mamma Mia</p>   
            </div>

            <div class="navbar"> 
                <ul>
                    <li><a class="active" href="index.html">Strona Główna</a></li>
                    <li><a href="order.php">Zamowienia</a></li>
                    <li><a href="contact.html">Kontakt</a></li>
                    <li><a href="about.html">O nas</a></li>
                  </ul>
            </div>

        </div>
        <div class="main">
            <h1>Mamma Mia - Najlepsza włoska restauracja w Warszawie</h1>
            <div id="shopping-cart">
<div class="txt-heading">Koszyk</div>

<a id="btnEmpty" href="order.php?action=empty">Opróżnij</a>
<?php
if(isset($_SESSION["cart_item"])){
    $total_quantity = 0;
    $total_price = 0;
?>	
<table class="tbl-cart" cellpadding="10" cellspacing="1">
<tbody>
<tr>
<th style="text-align:left;">Nazwa</th>
<th style="text-align:left;">Kod</th>
<th style="text-align:right;" width="5%">Ilosc</th>
<th style="text-align:right;" width="10%">Cena za jeden</th>
<th style="text-align:right;" width="10%">Cena</th>
<th style="text-align:center;" width="5%">Usun</th>
</tr>	
<?php		
    foreach ($_SESSION["cart_item"] as $item){
        $item_price = $item["quantity"]*$item["price"];
		?>
				<tr>
				<td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
				<td><?php echo $item["code"]; ?></td>
				<td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
				<td  style="text-align:right;"><?php echo "zł ".$item["price"]; ?></td>
				<td  style="text-align:right;"><?php echo "zł ". number_format($item_price,2); ?></td>
				<td style="text-align:center;"><a href="order.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"><img src="img/icon-delete.png" alt="Remove Item" /></a></td>
				</tr>
                <tr>
                    <form action="order.php">
                <td colspan='5' style="text-align:left;">
                <label>Imie </label>
                <input type="text" class="product-quantity" name="imie"  size="3" />
                <label>Nazwisko </label>
                <input type="text" class="product-quantity" name="Nazwisko"  size="3" />
                <label>Ulica </label>
                <input type="text" class="product-quantity" name="Ulica"  size="3" />
                <label>Numer domu/Mieszkania </label>
                <input type="text" class="product-quantity" name="Numer"  size="3" />
                <label>Kod Pocztowy </label>
                <input type="text" class="product-quantity" name="Kod"  size="3" />
                <a id="btnEmpty" href="order.php?action=zamow">Zamow</a>
                </td>
                </tr>
                </form>
				<?php
				$total_quantity += $item["quantity"];
				$total_price += ($item["price"]*$item["quantity"]);
		}
		?>

<tr>
<td colspan="2" align="right">Total:</td>
<td align="right"><?php echo $total_quantity; ?></td>
<td align="right" colspan="2"><strong><?php echo "zł ".number_format($total_price, 2); ?></strong></td>
<td></td>
</tr>
</tbody>
</table>		
  <?php
} else {
?>
<div class="no-records">Koszyk pusty</div>
<?php 
}
?>
</div>

<div id="product-grid">
	<div class="txt-heading">Produkty</div>
	<?php
	$product_array = $db_handle->runQuery("SELECT * FROM tblproduct ORDER BY id ASC");
	if (!empty($product_array)) { 
		foreach($product_array as $key=>$value){
	?>
		<div class="product-item">
			<form method="post" action="order.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>">
			<div class="product-image"><img style="width:270px;" src="<?php echo $product_array[$key]["image"]; ?>"></div>
			<div class="product-tile-footer">
			<div class="product-title"><?php echo $product_array[$key]["name"]; ?></div>
			<div class="product-price"><?php echo "$".$product_array[$key]["price"]; ?></div>
			<div class="cart-action"><input type="text" class="product-quantity" name="quantity" value="1" size="2" /><input type="submit" value="Dodaj do koszyka" class="btnAddAction" /></div>
			</div>
			</form>
		</div>
	<?php
		}
	}
	?>
</div>
        </div>
        <div class="footer">

            <div class="info">
                <p>
                    <h6>
                        Mamma Mia - włoska restauracja z rodzinnym podejsciem do życia <br>
                        Copyright® MAMMA MIA Z.O.O
                    </h6>
                </p>
            </div>
            <div class="links">
                <p>Mamma Mia - Emilian Bochenek</p>
                <ul class="footer-links">
                    <li><a href="help.html">Pomoc</a></li>
                    <li><a href="contact.html">Kontakt</a></li>
                    <li><a href="about.html">O nas</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>