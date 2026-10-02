<?php

session_start();

require_once __DIR__ . '/config/database.php';

// =====================================
// GET PRODUCT ID
// =====================================

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid product ID.");
}

// =====================================
// GET PRODUCT
// =====================================

$stmt = $conn->prepare(
    "SELECT * FROM products WHERE id = ?"
);

if (!$stmt) {
    die("Database Query Error: " . $conn->error);
}

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();

$stmt->close();

// =====================================
// PRODUCT NOT FOUND
// =====================================

if (!$product) {
    die("Product not found.");
}

// =====================================
// IMAGE NAME
// =====================================

$imageName = "";

if (!empty($product["image"])) {
    $imageName = basename($product["image"]);
}

// Image URL
$imageUrl = "";

if ($imageName !== "") {
    $imageUrl = "images/" . rawurlencode($imageName);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
<?= htmlspecialchars($product["name"]) ?> - S-Tech
</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f5f5;
}

/* =================================
   HEADER
================================= */

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

.cart {
    background: #111827;

    color: white;

    padding: 12px 20px;

    border-radius: 6px;

    text-decoration: none;

    font-weight: bold;
}

.cart:hover {
    background: #000;
}

/* =================================
   NAVIGATION
================================= */

.nav {
    background: #00a8ff;

    padding: 12px 5%;
}

.nav a {
    color: white;

    text-decoration: none;

    margin-right: 25px;

    font-weight: bold;
}

.nav a:hover {
    text-decoration: underline;
}

/* =================================
   CONTAINER
================================= */

.container {
    width: 90%;

    max-width: 1100px;

    margin: 40px auto;
}

/* =================================
   PRODUCT DETAILS
================================= */

.product-details {
    background: white;

    padding: 30px;

    border-radius: 12px;

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 40px;

    box-shadow:
        0 4px 15px rgba(0,0,0,.08);
}

/* =================================
   IMAGE AREA
================================= */

.image-box {
    width: 100%;

    height: 450px;

    background: #f5f5f5;

    border-radius: 10px;

    overflow: hidden;

    display: flex;

    align-items: center;

    justify-content: center;
}

.product-image {
    width: 100%;

    height: 100%;

    object-fit: contain;

    padding: 15px;
}

.no-image {
    width: 100%;

    height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-direction: column;

    color: #777;

    font-size: 70px;
}

.no-image span {
    font-size: 18px;

    margin-top: 10px;
}

/* =================================
   PRODUCT INFO
================================= */

.category {
    color: #777;

    margin-bottom: 10px;

    font-size: 15px;

    font-weight: bold;

    text-transform: uppercase;
}

h1 {
    font-size: 32px;

    margin: 0 0 15px 0;

    color: #111827;
}

.price {
    font-size: 30px;

    color: #00a8ff;

    font-weight: bold;
}

.old-price {
    color: #999;

    text-decoration: line-through;

    margin-left: 10px;

    font-size: 20px;
}

/* =================================
   STOCK
================================= */

.stock {
    margin-top: 20px;
}

.in-stock {
    color: green;

    font-weight: bold;
}

.out-stock {
    color: red;

    font-weight: bold;
}

/* =================================
   DESCRIPTION
================================= */

.description {
    margin-top: 25px;

    line-height: 1.7;

    color: #555;
}

.description h3 {
    color: #111827;

    margin-bottom: 10px;
}

/* =================================
   CART BUTTON
================================= */

.cart-btn {
    display: inline-block;

    margin-top: 25px;

    padding: 15px 30px;

    background: #00a8ff;

    color: white;

    text-decoration: none;

    border-radius: 7px;

    font-weight: bold;

    font-size: 16px;
}

.cart-btn:hover {
    background: #008ed6;
}

/* =================================
   BACK BUTTON
================================= */

.back-btn {
    display: inline-block;

    margin-top: 25px;

    margin-left: 10px;

    padding: 15px 25px;

    background: #6b7280;

    color: white;

    text-decoration: none;

    border-radius: 7px;

    font-weight: bold;
}

.back-btn:hover {
    background: #4b5563;
}

/* =================================
   FOOTER
================================= */

.footer {
    margin-top: 60px;

    padding: 40px;

    background: #111827;

    color: white;

    text-align: center;
}

/* =================================
   MOBILE
================================= */

@media(max-width: 700px) {

    .header {
        padding: 15px 20px;
    }

    .logo {
        font-size: 25px;
    }

    .cart {
        padding: 10px 15px;
    }

    .nav {
        padding: 12px 20px;
    }

    .nav a {
        display: inline-block;

        margin-right: 12px;

        margin-bottom: 8px;

        font-size: 14px;
    }

    .container {
        width: 95%;

        margin: 20px auto;
    }

    .product-details {
        grid-template-columns: 1fr;

        padding: 20px;

        gap: 25px;
    }

    .image-box {
        height: 300px;
    }

    h1 {
        font-size: 26px;
    }

    .price {
        font-size: 25px;
    }

    .back-btn {
        margin-left: 0;
    }
}

</style>

</head>

<body>

<!-- =================================
     HEADER
================================= -->

<div class="header">

    <div class="logo">
        S-<span>TECH</span>
    </div>

    <a href="cart.php" class="cart">
        🛒 Cart
    </a>

</div>


<!-- =================================
     NAVIGATION
================================= -->

<div class="nav">

    <a href="index.php">
        Home
    </a>

    <a href="index.php?category=Mobile">
        Mobile
    </a>

    <a href="index.php?category=Laptop">
        Laptop
    </a>

    <a href="index.php?category=Gadget">
        Gadget
    </a>

    <a href="index.php?category=Accessories">
        Accessories
    </a>

    <a href="index.php?category=Networking">
        Networking
    </a>

</div>


<!-- =================================
     PRODUCT
================================= -->

<div class="container">

    <div class="product-details">

        <!-- IMAGE -->

        <div>

            <div class="image-box">

                <?php if ($imageUrl !== ""): ?>

                    <img
                        src="<?= htmlspecialchars($imageUrl) ?>"
                        class="product-image"
                        alt="<?= htmlspecialchars($product["name"]) ?>"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >

                    <div
                        class="no-image"
                        style="display:none;"
                    >
                        📦

                        <span>
                            Image not available
                        </span>
                    </div>

                <?php else: ?>

                    <div class="no-image">

                        📦

                        <span>
                            No image available
                        </span>

                    </div>

                <?php endif; ?>

            </div>

        </div>


        <!-- PRODUCT INFORMATION -->

        <div>

            <!-- CATEGORY -->

            <div class="category">

                <?= htmlspecialchars(
                    $product["category"]
                ) ?>

            </div>


            <!-- NAME -->

            <h1>

                <?= htmlspecialchars(
                    $product["name"]
                ) ?>

            </h1>


            <!-- PRICE -->

            <div>

                <span class="price">

                    ৳<?= number_format(
                        (float)$product["price"],
                        2
                    ) ?>

                </span>

                <?php if (
                    isset($product["old_price"]) &&
                    (float)$product["old_price"] >
                    (float)$product["price"]
                ): ?>

                    <span class="old-price">

                        ৳<?= number_format(
                            (float)$product["old_price"],
                            2
                        ) ?>

                    </span>

                <?php endif; ?>

            </div>


            <!-- STOCK -->

            <div class="stock">

                <?php if (
                    (int)$product["stock"] > 0
                ): ?>

                    <span class="in-stock">

                        ✓ In Stock
                        (<?= (int)$product["stock"] ?>)

                    </span>

                <?php else: ?>

                    <span class="out-stock">

                        ✕ Out of Stock

                    </span>

                <?php endif; ?>

            </div>


            <!-- DESCRIPTION -->

            <div class="description">

                <h3>
                    Description
                </h3>

                <?php if (
                    !empty($product["description"])
                ): ?>

                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                $product["description"]
                            )
                        ) ?>

                    </p>

                <?php else: ?>

                    <p>
                        No description available.
                    </p>

                <?php endif; ?>

            </div>


            <!-- ADD TO CART -->

            <?php if (
                (int)$product["stock"] > 0
            ): ?>

                <a
                    href="cart.php?add=<?= (int)$product["id"] ?>"
                    class="cart-btn"
                >

                    🛒 Add to Cart

                </a>

            <?php endif; ?>


            <a
                href="index.php"
                class="back-btn"
            >

                ← Continue Shopping

            </a>

        </div>

    </div>

</div>


<!-- =================================
     FOOTER
================================= -->

<div class="footer">

    <h2>
        S-TECH
    </h2>

    <p>
        Your Trusted Online Technology Shop
    </p>

    <p>
        © <?= date("Y") ?> S-Tech Shop
    </p>

</div>

</body>

</html>