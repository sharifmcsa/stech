<?php

session_start();

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| Initialize Cart
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}


/*
|--------------------------------------------------------------------------
| Add Product
|--------------------------------------------------------------------------
*/

if (isset($_GET["add"])) {

    $id = intval($_GET["add"]);

    if ($id > 0) {

        $stmt = $conn->prepare(
            "SELECT id, name, price, image, stock
             FROM products
             WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $product = $result->fetch_assoc();


        if ($product && $product["stock"] > 0) {

            if (isset($_SESSION["cart"][$id])) {

                if (
                    $_SESSION["cart"][$id]["quantity"]
                    < $product["stock"]
                ) {

                    $_SESSION["cart"][$id]["quantity"]++;

                }

            } else {

                $_SESSION["cart"][$id] = [

                    "id" => $product["id"],

                    "name" => $product["name"],

                    "price" => $product["price"],

                    "image" => $product["image"],

                    "quantity" => 1

                ];

            }

        }

    }

    header("Location: cart.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Increase Quantity
|--------------------------------------------------------------------------
*/

if (isset($_GET["increase"])) {

    $id = intval($_GET["increase"]);

    if (isset($_SESSION["cart"][$id])) {

        $stmt = $conn->prepare(
            "SELECT stock FROM products WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $stock = $stmt
            ->get_result()
            ->fetch_assoc()["stock"];

        if (
            $_SESSION["cart"][$id]["quantity"]
            < $stock
        ) {

            $_SESSION["cart"][$id]["quantity"]++;

        }

    }

    header("Location: cart.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Decrease Quantity
|--------------------------------------------------------------------------
*/

if (isset($_GET["decrease"])) {

    $id = intval($_GET["decrease"]);

    if (isset($_SESSION["cart"][$id])) {

        $_SESSION["cart"][$id]["quantity"]--;

        if (
            $_SESSION["cart"][$id]["quantity"] <= 0
        ) {

            unset($_SESSION["cart"][$id]);

        }

    }

    header("Location: cart.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Remove Product
|--------------------------------------------------------------------------
*/

if (isset($_GET["remove"])) {

    $id = intval($_GET["remove"]);

    unset($_SESSION["cart"][$id]);

    header("Location: cart.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Calculate Total
|--------------------------------------------------------------------------
*/

$total = 0;

foreach ($_SESSION["cart"] as $item) {

    $total +=
        $item["price"] *
        $item["quantity"];

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Shopping Cart - S-Tech</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;

    font-family: Arial, sans-serif;

    background: #f5f5f5;
}

.header {
    background: white;

    padding: 18px 5%;

    display: flex;

    justify-content: space-between;

    align-items: center;

    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.logo {
    font-size: 30px;

    font-weight: bold;
}

.logo span {
    color: #00a8ff;
}

.home-btn {
    background: #111827;

    color: white;

    padding: 11px 18px;

    text-decoration: none;

    border-radius: 6px;
}

.container {
    width: 90%;

    max-width: 1100px;

    margin: 40px auto;
}

h1 {
    margin-bottom: 25px;
}

.cart-box {
    background: white;

    border-radius: 10px;

    overflow: hidden;

    box-shadow:
        0 3px 12px rgba(0,0,0,.08);
}

.cart-item {
    display: grid;

    grid-template-columns:
        100px 1fr 120px 130px 100px;

    gap: 20px;

    align-items: center;

    padding: 20px;

    border-bottom: 1px solid #eee;
}

.product-img {
    width: 90px;

    height: 90px;

    object-fit: cover;

    border-radius: 7px;
}

.product-name {
    font-weight: bold;

    font-size: 17px;
}

.price {
    color: #00a8ff;

    font-weight: bold;
}

.quantity {
    display: flex;

    align-items: center;

    gap: 8px;
}

.quantity a {
    background: #111827;

    color: white;

    width: 28px;

    height: 28px;

    display: flex;

    justify-content: center;

    align-items: center;

    text-decoration: none;

    border-radius: 4px;
}

.remove {
    color: red;

    text-decoration: none;

    font-weight: bold;
}

.cart-summary {
    background: white;

    margin-top: 25px;

    padding: 25px;

    border-radius: 10px;

    text-align: right;
}

.total {
    font-size: 25px;

    font-weight: bold;

    margin-bottom: 20px;
}

.checkout {
    display: inline-block;

    background: #00a8ff;

    color: white;

    text-decoration: none;

    padding: 14px 30px;

    border-radius: 6px;

    font-weight: bold;
}

.empty {
    background: white;

    padding: 60px;

    text-align: center;

    border-radius: 10px;
}

.empty a {
    display: inline-block;

    margin-top: 20px;

    background: #00a8ff;

    color: white;

    padding: 12px 25px;

    text-decoration: none;

    border-radius: 6px;
}

@media(max-width: 700px) {

    .cart-item {

        grid-template-columns: 80px 1fr;

    }

    .quantity,
    .remove,
    .cart-item > div:nth-child(4) {

        grid-column: 2;

    }

}

</style>

</head>

<body>


<div class="header">

    <div class="logo">
        S-<span>TECH</span>
    </div>

    <a href="index.php"
       class="home-btn">

        ← Continue Shopping

    </a>

</div>


<div class="container">

<h1>
🛒 Shopping Cart
</h1>


<?php if (!empty($_SESSION["cart"])): ?>


<div class="cart-box">


<?php foreach ($_SESSION["cart"] as $item): ?>


<div class="cart-item">


<!-- Image -->

<div>

<?php if (!empty($item["image"])): ?>

<img
    src="images/<?= htmlspecialchars(
        $item["image"]
    ) ?>"
    class="product-img">

<?php else: ?>

<div
style="
width:90px;
height:90px;
background:#eee;
display:flex;
align-items:center;
justify-content:center;
font-size:35px;
">

📦

</div>

<?php endif; ?>

</div>


<!-- Name -->

<div>

<div class="product-name">

<?= htmlspecialchars(
    $item["name"]
) ?>

</div>

<br>

<div class="price">

৳<?= number_format(
    $item["price"],
    2
) ?>

</div>

</div>


<!-- Quantity -->

<div class="quantity">

<a
href="cart.php?decrease=<?= $item["id"] ?>">

−

</a>

<strong>

<?= $item["quantity"] ?>

</strong>

<a
href="cart.php?increase=<?= $item["id"] ?>">

+

</a>

</div>


<!-- Subtotal -->

<div>

৳<?= number_format(
    $item["price"] *
    $item["quantity"],
    2
) ?>

</div>


<!-- Remove -->

<div>

<a
href="cart.php?remove=<?= $item["id"] ?>"
class="remove">

✕ Remove

</a>

</div>


</div>


<?php endforeach; ?>


</div>


<div class="cart-summary">

<div class="total">

Total:

৳<?= number_format(
    $total,
    2
) ?>

</div>


<a
href="checkout.php"
class="checkout">

Proceed to Checkout →

</a>

</div>


<?php else: ?>


<div class="empty">

<h2>
🛒 Your Cart is Empty
</h2>

<p>
আপনার cart-এ এখনো কোনো product নেই।
</p>

<a href="index.php">

Start Shopping

</a>

</div>


<?php endif; ?>


</div>


</body>

</html>