const products = [

    {
        id: 1,
        name: "Samsung Galaxy A55",
        category: "Mobile",
        price: 42999,
        oldPrice: 45999,
        icon: "📱"
    },

    {
        id: 2,
        name: "iPhone 15",
        category: "Mobile",
        price: 89999,
        oldPrice: 95000,
        icon: "📱"
    },

    {
        id: 3,
        name: "HP Laptop",
        category: "Laptop",
        price: 65000,
        oldPrice: 70000,
        icon: "💻"
    },

    {
        id: 4,
        name: "Lenovo IdeaPad",
        category: "Laptop",
        price: 58000,
        oldPrice: 62000,
        icon: "💻"
    },

    {
        id: 5,
        name: "Smart Watch Pro",
        category: "Gadget",
        price: 2999,
        oldPrice: 3999,
        icon: "⌚"
    },

    {
        id: 6,
        name: "Wireless Earbuds",
        category: "Gadget",
        price: 1999,
        oldPrice: 2499,
        icon: "🎧"
    },

    {
        id: 7,
        name: "Bluetooth Speaker",
        category: "Gadget",
        price: 2499,
        oldPrice: 2999,
        icon: "🔊"
    },

    {
        id: 8,
        name: "65W Fast Charger",
        category: "Accessories",
        price: 1499,
        oldPrice: 1899,
        icon: "🔌"
    }

];


let cart = [];


// SHOW PRODUCTS

function displayProducts(productList = products) {

    const grid = document.getElementById("productGrid");

    grid.innerHTML = "";


    if (productList.length === 0) {

        grid.innerHTML = `
            <p style="grid-column:1/-1;text-align:center">
                No products found.
            </p>
        `;

        return;
    }


    productList.forEach(product => {

        const card = document.createElement("div");

        card.className = "product-card";


        card.innerHTML = `

            <div class="product-image">
                ${product.icon}
            </div>

            <div class="product-info">

                <span class="product-category">
                    ${product.category}
                </span>

                <h3 class="product-name">
                    ${product.name}
                </h3>

                <div>

                    <span class="price">
                        ৳${product.price.toLocaleString()}
                    </span>

                    <span class="old-price">
                        ৳${product.oldPrice.toLocaleString()}
                    </span>

                </div>

                <button
                    class="add-cart"
                    onclick="addToCart(${product.id})">
                    🛒 Add to Cart
                </button>

            </div>

        `;


        grid.appendChild(card);

    });

}


// ADD TO CART

function addToCart(id) {

    const product = products.find(p => p.id === id);

    cart.push(product);

    updateCartCount();

    alert(product.name + " added to cart!");

}


// CART COUNT

function updateCartCount() {

    document.getElementById("cartCount").textContent = cart.length;

}


// OPEN CART

function openCart() {

    const modal = document.getElementById("cartModal");

    const cartItems = document.getElementById("cartItems");

    const cartTotal = document.getElementById("cartTotal");


    cartItems.innerHTML = "";


    if (cart.length === 0) {

        cartItems.innerHTML = "<p>Your cart is empty.</p>";

        cartTotal.textContent = "0";

    } else {

        let total = 0;


        cart.forEach((product, index) => {

            total += product.price;


            cartItems.innerHTML += `

                <div class="cart-item">

                    <span>
                        ${product.name}
                    </span>

                    <strong>
                        ৳${product.price.toLocaleString()}
                    </strong>

                    <button
                        onclick="removeFromCart(${index})">
                        ❌
                    </button>

                </div>

            `;

        });


        cartTotal.textContent = total.toLocaleString();

    }


    modal.style.display = "block";

}


// CLOSE CART

function closeCart() {

    document.getElementById("cartModal").style.display = "none";

}


// REMOVE CART ITEM

function removeFromCart(index) {

    cart.splice(index, 1);

    updateCartCount();

    openCart();

}


// CATEGORY FILTER

function filterCategory(category) {

    const filtered = products.filter(
        product => product.category === category
    );

    displayProducts(filtered);

    scrollToProducts();

}


// SHOW ALL

function showAllProducts() {

    displayProducts(products);

    scrollToProducts();

}


// SEARCH

document
    .getElementById("searchForm")
    .addEventListener("submit", function(event) {

        event.preventDefault();

        const keyword =
            document
            .getElementById("searchInput")
            .value
            .toLowerCase()
            .trim();


        const result = products.filter(product =>

            product.name
                .toLowerCase()
                .includes(keyword)

            ||

            product.category
                .toLowerCase()
                .includes(keyword)

        );


        displayProducts(result);

        scrollToProducts();

    });


// SCROLL

function scrollToProducts() {

    document
        .getElementById("products")
        .scrollIntoView({
            behavior: "smooth"
        });

}


// WISHLIST

function showWishlist() {

    alert("Wishlist feature will be added soon.");

}


// INITIAL LOAD

displayProducts();