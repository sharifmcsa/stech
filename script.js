/* =========================================================
   EMAILJS CONFIGURATION
========================================================= */

const EMAILJS_PUBLIC_KEY = "jHYMESrzv7Mk1VxFg";
const EMAILJS_SERVICE_ID = "service_v4yeshf";
const EMAILJS_TEMPLATE_ID = "template_vc4cy58";

emailjs.init({
    publicKey: EMAILJS_PUBLIC_KEY
});


/* =========================
   S-TECH SHOP PRODUCTS
========================= */

const products = [
    {
        id: 1,
        name: "TP-Link WiFi Router",
        category: "Networking",
        price: 1850,
        icon: "📡"
    },
    {
        id: 2,
        name: "MikroTik hEX Router",
        category: "Networking",
        price: 7500,
        icon: "🌐"
    },
    {
        id: 3,
        name: "8 Port Gigabit Switch",
        category: "Networking",
        price: 2200,
        icon: "🔌"
    },
    {
        id: 4,
        name: "Dell Core i5 Laptop",
        category: "Laptop",
        price: 48500,
        icon: "💻"
    },
    {
        id: 5,
        name: "HP Laptop 15",
        category: "Laptop",
        price: 55000,
        icon: "💻"
    },
    {
        id: 6,
        name: "Samsung Smartphone",
        category: "Mobile",
        price: 28500,
        icon: "📱"
    },
    {
        id: 7,
        name: "Wireless Keyboard",
        category: "Accessories",
        price: 1200,
        icon: "⌨️"
    },
    {
        id: 8,
        name: "Gaming Mouse",
        category: "Accessories",
        price: 950,
        icon: "🖱️"
    },
    {
        id: 9,
        name: "CCTV Camera 2MP",
        category: "CCTV",
        price: 3200,
        icon: "📹"
    },
    {
        id: 10,
        name: "CCTV DVR 4 Channel",
        category: "CCTV",
        price: 5500,
        icon: "📹"
    },
    {
        id: 11,
        name: "650VA UPS",
        category: "Power",
        price: 3500,
        icon: "🔋"
    },
    {
        id: 12,
        name: "12V Power Adapter",
        category: "Power",
        price: 650,
        icon: "🔌"
    }
];


/* =========================
   CART & WISHLIST
========================= */

let cart =
    JSON.parse(localStorage.getItem("stechCart")) || [];

let wishlist =
    JSON.parse(localStorage.getItem("stechWishlist")) || [];

let currentProducts = [...products];


/* =========================
   DISPLAY PRODUCTS
========================= */

function displayProducts(list = products) {

    const grid =
        document.getElementById("productGrid");

    grid.innerHTML = "";

    if (list.length === 0) {

        grid.innerHTML = `
            <div style="
                grid-column:1/-1;
                text-align:center;
                padding:50px;
            ">
                <h2>😔 No products found</h2>
                <p>Try another search.</p>
            </div>
        `;

        return;
    }


    list.forEach(product => {

        const liked =
            wishlist.includes(product.id);

        grid.innerHTML += `

            <div class="product">

                <div class="product-img">
                    ${product.icon}
                </div>

                <div class="product-info">

                    <div class="product-category">
                        ${product.category}
                    </div>

                    <h3>
                        ${product.name}
                    </h3>

                    <div class="price">
                        ৳${product.price.toLocaleString()}
                    </div>

                    <div class="product-actions">

                        <button
                            class="add-cart"
                            onclick="addToCart(${product.id})">

                            🛒 Add to Cart

                        </button>

                        <button
                            class="wishlist-btn"
                            onclick="toggleWishlist(${product.id})">

                            ${liked ? "❤️" : "♡"}

                        </button>

                    </div>

                </div>

            </div>

        `;
    });


    document.getElementById("resultText").innerText =
        `${list.length} product(s) found`;
}


/* =========================
   ADD TO CART
========================= */

function addToCart(id) {

    const product =
        products.find(p => p.id === id);

    if (!product) return;

    const existing =
        cart.find(item => item.id === id);

    if (existing) {

        existing.quantity++;

    } else {

        cart.push({
            ...product,
            quantity: 1
        });

    }

    saveCart();

    updateCartCount();

    alert(
        "✅ " +
        product.name +
        " added to cart!"
    );
}


/* =========================
   SAVE CART
========================= */

function saveCart() {

    localStorage.setItem(
        "stechCart",
        JSON.stringify(cart)
    );
}


/* =========================
   CART COUNT
========================= */

function updateCartCount() {

    const count =
        cart.reduce(
            (total, item) =>
                total + item.quantity,
            0
        );

    document.getElementById("cartCount").innerText =
        count;
}


/* =========================
   OPEN CART
========================= */

function openCart() {

    renderCart();

    document
        .getElementById("cartModal")
        .classList.add("show");
}


/* =========================
   CLOSE CART
========================= */

function closeCart() {

    document
        .getElementById("cartModal")
        .classList.remove("show");
}


/* =========================
   DISPLAY CART
========================= */

function renderCart() {

    const container =
        document.getElementById("cartItems");

    container.innerHTML = "";

    if (cart.length === 0) {

        container.innerHTML = `
            <div style="
                text-align:center;
                padding:30px;
            ">

                <div style="font-size:50px;">
                    🛒
                </div>

                <h3>
                    Your cart is empty
                </h3>

                <p>
                    Add some products first.
                </p>

            </div>
        `;

        document.getElementById(
            "cartTotal"
        ).innerText = "৳0";

        return;
    }


    let total = 0;


    cart.forEach(item => {

        const itemTotal =
            item.price * item.quantity;

        total += itemTotal;


        container.innerHTML += `

            <div class="cart-item">

                <div class="cart-item-info">

                    <strong>
                        ${item.icon}
                        ${item.name}
                    </strong>

                    <small>
                        ৳${item.price.toLocaleString()}
                    </small>

                </div>


                <div class="qty">

                    <button
                        onclick="changeQuantity(${item.id},-1)">
                        −
                    </button>

                    <strong>
                        ${item.quantity}
                    </strong>

                    <button
                        onclick="changeQuantity(${item.id},1)">
                        +
                    </button>

                </div>


                <strong>
                    ৳${itemTotal.toLocaleString()}
                </strong>


                <button
                    class="remove"
                    onclick="removeFromCart(${item.id})">

                    ✕

                </button>

            </div>

        `;
    });


    document.getElementById(
        "cartTotal"
    ).innerText =
        "৳" + total.toLocaleString();
}


/* =========================
   CHANGE QUANTITY
========================= */

function changeQuantity(id, amount) {

    const item =
        cart.find(
            product => product.id === id
        );

    if (!item) return;

    item.quantity += amount;


    if (item.quantity <= 0) {

        cart =
            cart.filter(
                product => product.id !== id
            );
    }


    saveCart();

    updateCartCount();

    renderCart();
}


/* =========================
   REMOVE PRODUCT
========================= */

function removeFromCart(id) {

    cart =
        cart.filter(
            product => product.id !== id
        );

    saveCart();

    updateCartCount();

    renderCart();
}


/* =========================
   CATEGORY FILTER
========================= */

function filterCategory(category) {

    currentProducts =
        products.filter(
            product =>
                product.category === category
        );

    displayProducts(
        currentProducts
    );


    document
        .getElementById("products")
        .scrollIntoView({
            behavior: "smooth"
        });
}


/* =========================
   SEARCH
========================= */

document
    .getElementById("searchForm")
    .addEventListener(
        "submit",
        function(e) {

            e.preventDefault();

            const keyword =
                document
                    .getElementById("searchInput")
                    .value
                    .toLowerCase()
                    .trim();


            if (keyword === "") {

                currentProducts =
                    [...products];

            } else {

                currentProducts =
                    products.filter(
                        product =>

                            product.name
                                .toLowerCase()
                                .includes(keyword)

                            ||

                            product.category
                                .toLowerCase()
                                .includes(keyword)
                    );
            }


            displayProducts(
                currentProducts
            );


            document
                .getElementById("products")
                .scrollIntoView({
                    behavior: "smooth"
                });

        }
    );


/* =========================
   SORT PRODUCTS
========================= */

function sortProducts() {

    const value =
        document
            .getElementById("sortSelect")
            .value;


    if (value === "low") {

        currentProducts.sort(
            (a, b) =>
                a.price - b.price
        );

    }

    else if (value === "high") {

        currentProducts.sort(
            (a, b) =>
                b.price - a.price
        );

    }

    else {

        currentProducts =
            [...products];

    }


    displayProducts(
        currentProducts
    );
}


/* =========================
   WISHLIST
========================= */

function toggleWishlist(id) {

    if (wishlist.includes(id)) {

        wishlist =
            wishlist.filter(
                productId =>
                    productId !== id
            );

    } else {

        wishlist.push(id);

    }


    localStorage.setItem(
        "stechWishlist",
        JSON.stringify(wishlist)
    );


    displayProducts(
        currentProducts
    );
}


/* =========================
   SHOW WISHLIST
========================= */

function showWishlist() {

    if (wishlist.length === 0) {

        alert(
            "❤️ Your wishlist is empty."
        );

        return;
    }


    const items =
        products.filter(
            product =>
                wishlist.includes(product.id)
        );


    currentProducts = items;


    displayProducts(items);


    document
        .getElementById("products")
        .scrollIntoView({
            behavior: "smooth"
        });
}


/* =========================================================
   CHECKOUT
========================================================= */

function checkout() {

    if (cart.length === 0) {

        alert(
            "Your cart is empty!"
        );

        return;
    }


    /* Close cart */

    closeCart();


    /* Show checkout */

    document
        .getElementById("checkoutModal")
        .classList.add("show");


    renderCheckout();
}


/* =========================================================
   RENDER CHECKOUT
========================================================= */

function renderCheckout() {

    const container =
        document.getElementById(
            "checkoutItems"
        );

    container.innerHTML = "";


    let total = 0;


    cart.forEach(item => {

        const subtotal =
            item.price * item.quantity;

        total += subtotal;


        container.innerHTML += `

            <div class="checkout-item">

                <span>
                    ${item.icon}
                    ${item.name}
                    × ${item.quantity}
                </span>

                <strong>
                    ৳${subtotal.toLocaleString()}
                </strong>

            </div>

        `;
    });


    document.getElementById(
        "checkoutTotal"
    ).innerText =
        "৳" + total.toLocaleString();
}


/* =========================================================
   CLOSE CHECKOUT
========================================================= */

function closeCheckout() {

    document
        .getElementById("checkoutModal")
        .classList.remove("show");
}


/* =========================================================
   PLACE ORDER
========================================================= */

document
    .getElementById("checkoutForm")
    .addEventListener(
        "submit",
        function(e) {

            e.preventDefault();


            if (cart.length === 0) {

                alert(
                    "Your cart is empty!"
                );

                return;
            }


            /* Customer information */

            const name =
                document
                    .getElementById("customerName")
                    .value
                    .trim();


            const phone =
                document
                    .getElementById("customerPhone")
                    .value
                    .trim();


            const address =
                document
                    .getElementById("customerAddress")
                    .value
                    .trim();


            const note =
                document
                    .getElementById("customerNote")
                    .value
                    .trim();


            if (!name || !phone || !address) {

                alert(
                    "Please fill all required fields."
                );

                return;
            }


            /* Calculate total */

            const total =
                cart.reduce(
                    (sum, item) =>
                        sum +
                        item.price *
                        item.quantity,
                    0
                );


            /* Generate Order ID */

            const orderId =
                "ST-" +
                Date.now()
                    .toString()
                    .slice(-8);


            /* Order products */

            let orderItems = "";


            cart.forEach(
                (item, index) => {

                    const subtotal =
                        item.price *
                        item.quantity;


                    orderItems +=
                        `${index + 1}. ${item.name}\n` +
                        `   Price: ৳${item.price.toLocaleString()}\n` +
                        `   Quantity: ${item.quantity}\n` +
                        `   Subtotal: ৳${subtotal.toLocaleString()}\n\n`;
                }
            );


            /* Date */

            const orderDate =
                new Date().toLocaleString(
                    "en-BD",
                    {
                        dateStyle: "medium",
                        timeStyle: "short"
                    }
                );


            /* EmailJS parameters */

            const templateParams = {

                order_id: orderId,

                customer_name: name,

                customer_phone: phone,

                customer_address: address,

                customer_note:
                    note || "No note",

                order_items:
                    orderItems,

                order_total:
                    "৳" +
                    total.toLocaleString(),

                order_date:
                    orderDate

            };


            /* Button */

            const button =
                document.getElementById(
                    "placeOrderBtn"
                );


            button.disabled = true;

            button.innerText =
                "⏳ Sending Order...";


            /* Send Email */

            emailjs.send(
                EMAILJS_SERVICE_ID,
                EMAILJS_TEMPLATE_ID,
                templateParams
            )

            .then(function(response) {

                console.log(
                    "Order email sent:",
                    response
                );


                alert(
                    "✅ Order Successfully Placed!\n\n" +
                    "Order ID: " +
                    orderId +
                    "\n\n" +
                    "Your order request has been sent to S-Tech Shop."
                );


                /* Empty cart */

                cart = [];


                saveCart();

                updateCartCount();


                /* Reset form */

                document
                    .getElementById(
                        "checkoutForm"
                    )
                    .reset();


                closeCheckout();

            })

            .catch(function(error) {

                console.error(
                    "EmailJS Error:",
                    error
                );


                alert(
                    "❌ Order পাঠানো যায়নি!\n\n" +
                    "EmailJS configuration অথবা Template settings check করুন."
                );

            })

            .finally(function() {

                button.disabled = false;

                button.innerText =
                    "📧 Place Order";

            });

        }
    );


/* =========================================================
   CLOSE CART OUTSIDE
========================================================= */

document
    .getElementById("cartModal")
    .addEventListener(
        "click",
        function(e) {

            if (e.target === this) {

                closeCart();

            }

        }
    );


/* =========================================================
   CLOSE CHECKOUT OUTSIDE
========================================================= */

document
    .getElementById("checkoutModal")
    .addEventListener(
        "click",
        function(e) {

            if (e.target === this) {

                closeCheckout();

            }

        }
    );


/* =========================================================
   INITIAL LOAD
========================================================= */

displayProducts();

updateCartCount();