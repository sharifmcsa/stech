<?php
// S-Tech Shop main page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S-Tech Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

     <!--<h1>Welcome to S-Tech Shop</h1>
   <p>PHP + MySQL project is ready.</p>-->

    <script src="script.js"></script>
</body>
</html>
<?php

require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Category Filter
|--------------------------------------------------------------------------
*/

$category = trim($_GET["category"] ?? "");

$search = trim($_GET["search"] ?? "");


/*
|--------------------------------------------------------------------------
| Get Products
|--------------------------------------------------------------------------
*/

if ($category !== "" && $search !== "") {

    $stmt = $conn->prepare(
        "SELECT *
         FROM products
         WHERE category = ?
         AND name LIKE ?
         ORDER BY id DESC"
    );

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param(
        "ss",
        $category,
        $searchTerm
    );

    $stmt->execute();

    $products = $stmt->get_result();

} elseif ($category !== "") {

    $stmt = $conn->prepare(
        "SELECT *
         FROM products
         WHERE category = ?
         ORDER BY id DESC"
    );

    $stmt->bind_param(
        "s",
        $category
    );

    $stmt->execute();

    $products = $stmt->get_result();

} elseif ($search !== "") {

    $stmt = $conn->prepare(
        "SELECT *
         FROM products
         WHERE name LIKE ?
         OR category LIKE ?
         ORDER BY id DESC"
    );

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param(
        "ss",
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $products = $stmt->get_result();

} else {

    $products = $conn->query(
        "SELECT *
         FROM products
         ORDER BY id DESC"
    );

}


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

$categories = $conn->query(
    "SELECT DISTINCT category
     FROM products
     WHERE category IS NOT NULL
     AND category != ''
     ORDER BY category"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>
S-Tech Shop | Online Store
</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f5f7fa;
    color: #222;
}


/* TOPBAR */

.topbar {
    background: #111827;
    color: white;

    padding: 9px 5%;

    font-size: 14px;

    display: flex;

    justify-content: space-between;

    flex-wrap: wrap;

    gap: 10px;
}


/* HEADER */

.header {
    background: white;

    padding: 18px 5%;

    display: flex;

    align-items: center;

    gap: 30px;

    box-shadow:
        0 2px 10px rgba(0,0,0,.08);

    position: sticky;

    top: 0;

    z-index: 100;
}

.logo {
    font-size: 30px;

    font-weight: 800;

    white-space: nowrap;
}

.logo span {
    color: #00a8ff;
}


/* SEARCH */

.search {
    flex: 1;

    display: flex;

    max-width: 650px;
}

.search input {
    width: 100%;

    padding: 13px 16px;

    border: 1px solid #ddd;

    border-right: none;

    border-radius: 7px 0 0 7px;

    outline: none;

    font-size: 15px;
}

.search button {
    padding: 0 22px;

    border: none;

    background: #00a8ff;

    color: white;

    border-radius: 0 7px 7px 0;

    cursor: pointer;

    font-size: 18px;
}


/* CART */

.cart {
    text-decoration: none;

    background: #111827;

    color: white;

    padding: 12px 18px;

    border-radius: 7px;

    white-space: nowrap;
}


/* NAVIGATION */

.nav {
    background: #00a8ff;

    padding: 12px 5%;

    display: flex;

    gap: 25px;

    overflow-x: auto;
}

.nav a {
    color: white;

    text-decoration: none;

    font-weight: bold;

    white-space: nowrap;
}

.nav a:hover {
    text-decoration: underline;
}


/* HERO */

.hero {
    width: 90%;

    max-width: 1400px;

    margin: 25px auto;

    padding: 65px 50px;

    border-radius: 15px;

    background:
        linear-gradient(
            135deg,
            #00a8ff,
            #0066ff
        );

    color: white;

    text-align: center;
}

.hero h1 {
    font-size: 42px;

    margin-bottom: 12px;
}

.hero p {
    font-size: 18px;

    margin-bottom: 25px;
}

.shop-btn {
    display: inline-block;

    padding: 13px 28px;

    background: white;

    color: #0066ff;

    text-decoration: none;

    border-radius: 7px;

    font-weight: bold;
}


/* CONTAINER */

.container {
    width: 90%;

    max-width: 1400px;

    margin: 35px auto;
}


/* SECTION HEADER */

.section-header {
    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;

    gap: 15px;
}

.section-header h2 {
    font-size: 28px;
}


/* PRODUCTS */

.products {
    display: grid;

    grid-template-columns:
        repeat(
            auto-fill,
            minmax(220px, 1fr)
        );

    gap: 20px;
}


/* PRODUCT CARD */

.product {
    background: white;

    border-radius: 10px;

    overflow: hidden;

    box-shadow:
        0 3px 12px rgba(0,0,0,.08);

    transition: .2s;

    position: relative;
}

.product:hover {
    transform: translateY(-4px);

    box-shadow:
        0 7px 20px rgba(0,0,0,.12);
}


/* IMAGE */

.product-image {
    width: 100%;

    height: 230px;

    object-fit: contain;

    background: #f8f8f8;

    padding: 15px;
}

.no-image {
    height: 230px;

    background: #f1f1f1;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 65px;
}


/* PRODUCT INFO */

.product-info {
    padding: 17px;
}

.category {
    color: #777;

    font-size: 13px;

    margin-bottom: 7px;
}

.product-name {
    font-size: 17px;

    font-weight: bold;

    min-height: 45px;

    margin-bottom: 10px;
}

.price {
    color: #00a8ff;

    font-size: 21px;

    font-weight: bold;
}

.old-price {
    color: #999;

    text-decoration: line-through;

    font-size: 14px;

    margin-left: 6px;
}


/* BUTTONS */

.product-buttons {
    display: flex;

    gap: 8px;

    margin-top: 15px;
}

.view-btn,
.cart-btn {
    flex: 1;

    padding: 10px 8px;

    text-align: center;

    text-decoration: none;

    border-radius: 5px;

    font-weight: bold;

    font-size: 13px;
}

.view-btn {
    background: #eef2ff;

    color: #111827;
}

.cart-btn {
    background: #00a8ff;

    color: white;
}

.cart-btn:hover {
    background: #008ed6;
}


/* OUT OF STOCK */

.out-stock {
    display: block;

    margin-top: 15px;

    padding: 9px;

    background: #fee2e2;

    color: #b91c1c;

    text-align: center;

    border-radius: 5px;

    font-weight: bold;
}


/* EMPTY */

.empty {
    background: white;

    padding: 60px;

    border-radius: 10px;

    text-align: center;

    color: #666;
}


/* FOOTER */

.footer {
    margin-top: 60px;

    background: #111827;

    color: white;

    padding: 45px 5%;

    text-align: center;
}

.footer h2 {
    color: #00a8ff;

    margin-bottom: 10px;
}


/* MOBILE */

@media(max-width: 800px) {

    .header {
        flex-wrap: wrap;

        gap: 12px;
    }

    .search {
        order: 3;

        width: 100%;

        max-width: none;
    }

    .hero {
        padding: 45px 20px;
    }

    .hero h1 {
        font-size: 30px;
    }

}

@media(max-width: 500px) {

    .topbar {
        justify-content: center;

        text-align: center;
    }

    .logo {
        font-size: 25px;
    }

    .products {
        grid-template-columns:
            repeat(2, 1fr);

        gap: 10px;
    }

    .product-image,
    .no-image {
        height: 170px;
    }

    .product-info {
        padding: 10px;
    }

    .product-name {
        font-size: 14px;
    }

    .price {
        font-size: 17px;
    }

    .product-buttons {
        flex-direction: column;
    }

}

</style>

</head>

<body>


<!-- TOPBAR -->

<div class="topbar">

    <div>
        🚚 Free Delivery on selected orders
    </div>

    <div>
        ☎ 01713549817 |
        📧 support.stech.mym@gmail.com
    </div>

</div>


<!-- HEADER -->

<header class="header">


<div class="logo">

    S-<span>TECH</span>

</div>


<form
    class="search"
    method="GET"
    action="index.php">

    <?php if ($category !== ""): ?>

    <input
        type="hidden"
        name="category"
        value="<?= htmlspecialchars($category) ?>">

    <?php endif; ?>


    <input
        type="text"
        name="search"
        placeholder="Search products..."
        value="<?= htmlspecialchars($search) ?>">

    <button type="submit">
        🔍
    </button>

</form>


<a
    href="cart.php"
    class="cart">

    🛒 Cart

</a>


</header>


<!-- NAV -->

<nav class="nav">

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

</nav>


<!-- HERO -->

<section class="hero">

<h1>
Welcome to S-Tech Shop
</h1>

<p>
Your Trusted Online Technology Shop
</p>

<a
    href="#products"
    class="shop-btn">

    Shop Now

</a>

</section>


<!-- PRODUCTS -->

<div
    class="container"
    id="products">


<div class="section-header">

<h2>

<?php if ($search !== ""): ?>

Search Results for:
"<?= htmlspecialchars($search) ?>"

<?php elseif ($category !== ""): ?>

<?= htmlspecialchars($category) ?>

Products

<?php else: ?>

Latest Products

<?php endif; ?>

</h2>


<?php if (
    $category !== "" ||
    $search !== ""
): ?>

<a
    href="index.php"
    style="
        text-decoration:none;
        color:#00a8ff;
        font-weight:bold;
    ">

    ✕ Clear Filter

</a>

<?php endif; ?>


</div>


<div class="products">


<?php if (
    $products &&
    $products->num_rows > 0
): ?>


<?php while (
    $product = $products->fetch_assoc()
): ?>


<div class="product">


<!-- IMAGE -->

<?php if (
    !empty($product["image"])
): ?>

<img
    src="images/<?= htmlspecialchars(
        $product["image"]
    ) ?>"
    class="product-image"
    alt="<?= htmlspecialchars(
        $product["name"]
    ) ?>">

<?php else: ?>

<div class="no-image">
    📦
</div>

<?php endif; ?>


<!-- INFO -->

<div class="product-info">


<div class="category">

<?= htmlspecialchars(
    $product["category"]
) ?>

</div>


<div class="product-name">

<?= htmlspecialchars(
    $product["name"]
) ?>

</div>


<div>


<span class="price">

৳<?= number_format(
    $product["price"],
    2
) ?>

</span>


<?php if (
    isset($product["old_price"]) &&
    $product["old_price"] >
    $product["price"]
): ?>

<span class="old-price">

৳<?= number_format(
    $product["old_price"],
    2
) ?>

</span>

<?php endif; ?>


</div>


<?php if (
    isset($product["stock"]) &&
    $product["stock"] > 0
): ?>


<div class="product-buttons">


<a
    href="product.php?id=<?= $product["id"] ?>"
    class="view-btn">

    View

</a>


<a
    href="cart.php?add=<?= $product["id"] ?>"
    class="cart-btn">

    🛒 Add Cart

</a>


</div>


<?php else: ?>


<div class="out-stock">

Out of Stock

</div>


<?php endif; ?>


</div>

</div>


<?php endwhile; ?>


<?php else: ?>


<div class="empty"
     style="grid-column:1/-1;">

<h2>
📦 No Products Found
</h2>

<p style="margin-top:10px;">

Admin Panel থেকে product add করুন।

</p>

<a
    href="admin/add-product.php"
    style="
        display:inline-block;
        margin-top:20px;
        padding:12px 20px;
        background:#00a8ff;
        color:white;
        text-decoration:none;
        border-radius:6px;
    ">

    ➕ Add Product

</a>

</div>


<?php endif; ?>


</div>

</div>


<!-- FOOTER -->

<footer class="footer">

<h2>
S-TECH
</h2>

<p>
Mobile • Laptop • Gadget • Accessories
</p>

<p style="margin-top:15px;">

☎ 01713549817

</p>

<p style="margin-top:8px;">

📧 support.stech.mym@gmail.com

</p>

<p style="margin-top:20px;">

© <?= date("Y") ?> S-Tech Shop.
All Rights Reserved.

</p>

</footer>


</body>

</html>