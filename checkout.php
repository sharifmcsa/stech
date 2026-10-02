<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/config/database.php";

/*
|--------------------------------------------------------------------------
| Check Database Connection
|--------------------------------------------------------------------------
*/

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed.");
}


/*
|--------------------------------------------------------------------------
| Check Cart
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["cart"]) || !is_array($_SESSION["cart"]) || empty($_SESSION["cart"])) {
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

    $price = (float)($item["price"] ?? 0);
    $quantity = (int)($item["quantity"] ?? 0);

    if ($price < 0 || $quantity <= 0) {
        continue;
    }

    $total += $price * $quantity;
}


$error = "";


/*
|--------------------------------------------------------------------------
| Place Order
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $customer_name = trim($_POST["customer_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($customer_name === "") {

        $error = "Customer name is required.";

    } elseif ($phone === "") {

        $error = "Phone number is required.";

    } elseif ($address === "") {

        $error = "Delivery address is required.";

    } elseif ($total <= 0) {

        $error = "Invalid order total.";

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | Start Transaction
            |--------------------------------------------------------------------------
            */

            $conn->begin_transaction();


            /*
            |--------------------------------------------------------------------------
            | Insert Order
            |--------------------------------------------------------------------------
            */

            $status = "Pending";

            $sql = "
                INSERT INTO orders
                (
                    customer_name,
                    phone,
                    address,
                    total_amount,
                    status
                )
                VALUES (?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception(
                    "Order table error: " . $conn->error
                );
            }


            $stmt->bind_param(
                "sssds",
                $customer_name,
                $phone,
                $address,
                $total,
                $status
            );


            if (!$stmt->execute()) {

                throw new Exception(
                    "Order insert failed: " . $stmt->error
                );
            }


            $order_id = $conn->insert_id;

            $stmt->close();


            /*
            |--------------------------------------------------------------------------
            | Insert Order Items
            |--------------------------------------------------------------------------
            |
            | আপনার order_items table অনুযায়ী:
            |
            | id
            | order_id
            | product_id
            | quantity
            | price
            | subtotal
            |
            |--------------------------------------------------------------------------
            */

            $item_sql = "
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    quantity,
                    price,
                    subtotal
                )
                VALUES (?, ?, ?, ?, ?)
            ";

            $item_stmt = $conn->prepare($item_sql);

            if (!$item_stmt) {

                throw new Exception(
                    "Order Items table error: " .
                    $conn->error
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Stock Update
            |--------------------------------------------------------------------------
            */

            $stock_sql = "
                UPDATE products
                SET stock = stock - ?
                WHERE id = ?
                AND stock >= ?
            ";

            $stock_stmt = $conn->prepare($stock_sql);

            if (!$stock_stmt) {

                throw new Exception(
                    "Stock query error: " .
                    $conn->error
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Process Cart
            |--------------------------------------------------------------------------
            */

            foreach ($_SESSION["cart"] as $item) {

                $product_id = (int)($item["id"] ?? 0);

                $price = (float)($item["price"] ?? 0);

                $quantity = (int)($item["quantity"] ?? 0);


                if ($product_id <= 0) {

                    throw new Exception(
                        "Invalid product ID."
                    );
                }


                if ($quantity <= 0) {

                    throw new Exception(
                        "Invalid quantity."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Verify Current Product
                |--------------------------------------------------------------------------
                */

                $check_sql = "
                    SELECT id, name, price, stock
                    FROM products
                    WHERE id = ?
                    LIMIT 1
                ";

                $check_stmt = $conn->prepare($check_sql);

                if (!$check_stmt) {

                    throw new Exception(
                        "Product check error: " .
                        $conn->error
                    );
                }


                $check_stmt->bind_param(
                    "i",
                    $product_id
                );

                $check_stmt->execute();

                $check_result = $check_stmt->get_result();

                $db_product = $check_result->fetch_assoc();

                $check_stmt->close();


                if (!$db_product) {

                    throw new Exception(
                        "Product not found. ID: " .
                        $product_id
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Check Stock
                |--------------------------------------------------------------------------
                */

                if ((int)$db_product["stock"] < $quantity) {

                    throw new Exception(
                        $db_product["name"] .
                        " এর পর্যাপ্ত stock নেই।"
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Use Current Database Price
                |--------------------------------------------------------------------------
                */

                $price = (float)$db_product["price"];

                $subtotal = $price * $quantity;


                /*
                |--------------------------------------------------------------------------
                | Insert Order Item
                |--------------------------------------------------------------------------
                */

                $item_stmt->bind_param(
                    "iiidd",
                    $order_id,
                    $product_id,
                    $quantity,
                    $price,
                    $subtotal
                );


                if (!$item_stmt->execute()) {

                    throw new Exception(
                        "Order item insert failed: " .
                        $item_stmt->error
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Reduce Stock
                |--------------------------------------------------------------------------
                */

                $stock_stmt->bind_param(
                    "iii",
                    $quantity,
                    $product_id,
                    $quantity
                );


                if (!$stock_stmt->execute()) {

                    throw new Exception(
                        "Stock update failed: " .
                        $stock_stmt->error
                    );
                }


                if ($stock_stmt->affected_rows === 0) {

                    throw new Exception(
                        "Stock is not available."
                    );
                }
            }


            $item_stmt->close();

            $stock_stmt->close();


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            $conn->commit();


            /*
            |--------------------------------------------------------------------------
            | Clear Cart
            |--------------------------------------------------------------------------
            */

            $_SESSION["cart"] = [];


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            ?>

            <!DOCTYPE html>

            <html lang="en">

            <head>

                <meta charset="UTF-8">

                <meta
                    name="viewport"
                    content="width=device-width, initial-scale=1.0"
                >

                <title>
                    Order Successful - S-Tech
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

                    .success-box {
                        max-width: 600px;
                        margin: 100px auto;
                        background: white;
                        padding: 50px 30px;
                        text-align: center;
                        border-radius: 15px;
                        box-shadow: 0 5px 25px rgba(0,0,0,.10);
                    }

                    .icon {
                        font-size: 70px;
                    }

                    h1 {
                        color: #16a34a;
                    }

                    .order-id {
                        font-size: 23px;
                        font-weight: bold;
                        margin: 25px 0;
                    }

                    .btn {
                        display: inline-block;
                        margin-top: 20px;
                        padding: 14px 28px;
                        background: #00a8ff;
                        color: white;
                        text-decoration: none;
                        border-radius: 7px;
                        font-weight: bold;
                    }

                    .btn:hover {
                        background: #008ed6;
                    }

                </style>

            </head>

            <body>

                <div class="success-box">

                    <div class="icon">
                        ✅
                    </div>

                    <h1>
                        Order Successful!
                    </h1>

                    <p>
                        আপনার order সফলভাবে গ্রহণ করা হয়েছে।
                    </p>

                    <div class="order-id">
                        Order ID: #<?= (int)$order_id ?>
                    </div>

                    <p>
                        আমরা আপনার order শীঘ্রই process করব।
                    </p>

                    <a
                        href="index.php"
                        class="btn"
                    >
                        Continue Shopping
                    </a>

                </div>

            </body>

            </html>

            <?php

            exit;


        } catch (Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | Rollback
            |--------------------------------------------------------------------------
            */

            $conn->rollback();

            $error =
                "Order করা যায়নি: " .
                $e->getMessage();
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
Checkout - S-Tech
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

.back {
    background: #111827;
    color: white;
    text-decoration: none;
    padding: 11px 18px;
    border-radius: 6px;
}

.container {
    width: 90%;
    max-width: 1100px;
    margin: 40px auto;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 25px;
}

.box {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,.08);
}

.box h2 {
    margin-top: 0;
}

.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
}

input,
textarea {
    width: 100%;
    padding: 13px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

.place-order {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 6px;
    background: #00a8ff;
    color: white;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
}

.place-order:hover {
    background: #008ed6;
}

.error {
    background: #fee2e2;
    color: #991b1b;
    padding: 13px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.total {
    display: flex;
    justify-content: space-between;
    font-size: 22px;
    font-weight: bold;
    margin-top: 20px;
}

@media(max-width: 700px) {

    .checkout-grid {
        grid-template-columns: 1fr;
    }

    .container {
        width: 95%;
        margin: 20px auto;
    }

}

</style>

</head>


<body>


<div class="header">

    <div class="logo">
        S-<span>TECH</span>
    </div>

    <a
        href="cart.php"
        class="back"
    >
        ← Cart
    </a>

</div>


<div class="container">

    <h1>
        Checkout
    </h1>


    <?php if ($error !== ""): ?>

        <div class="error">

            ❌ <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <div class="checkout-grid">


        <!-- CUSTOMER INFORMATION -->

        <div class="box">

            <h2>
                Customer Information
            </h2>

            <form method="POST">

                <div class="form-group">

                    <label>
                        Full Name *
                    </label>

                    <input
                        type="text"
                        name="customer_name"
                        placeholder="আপনার নাম"
                        value="<?= htmlspecialchars(
                            $_POST["customer_name"] ?? ""
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Mobile Number *
                    </label>

                    <input
                        type="tel"
                        name="phone"
                        placeholder="01XXXXXXXXX"
                        value="<?= htmlspecialchars(
                            $_POST["phone"] ?? ""
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Delivery Address *
                    </label>

                    <textarea
                        name="address"
                        placeholder="আপনার সম্পূর্ণ ঠিকানা"
                        required
                    ><?= htmlspecialchars(
                        $_POST["address"] ?? ""
                    ) ?></textarea>

                </div>


                <button
                    type="submit"
                    class="place-order"
                >
                    🛍️ Place Order
                </button>

            </form>

        </div>


        <!-- ORDER SUMMARY -->

        <div class="box">

            <h2>
                Order Summary
            </h2>


            <?php foreach ($_SESSION["cart"] as $item): ?>

                <div class="summary-item">

                    <span>

                        <?= htmlspecialchars(
                            $item["name"] ?? ""
                        ) ?>

                        ×
                        <?= (int)(
                            $item["quantity"] ?? 0
                        ) ?>

                    </span>

                    <strong>

                        ৳<?= number_format(
                            (float)(
                                ($item["price"] ?? 0)
                                *
                                ($item["quantity"] ?? 0)
                            ),
                            2
                        ) ?>

                    </strong>

                </div>

            <?php endforeach; ?>


            <div class="total">

                <span>
                    Total
                </span>

                <span>
                    ৳<?= number_format(
                        $total,
                        2
                    ) ?>
                </span>

            </div>

        </div>


    </div>

</div>


</body>

</html>