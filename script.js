const products=[
{id:1,name:"TP-Link Archer C6 AC1200 Router",cat:"Networking",price:4250,old:4990,icon:"📡",rating:4.8},
{id:2,name:"Gigabit 8-Port Network Switch",cat:"Networking",price:2850,old:3200,icon:"🔌",rating:4.7},
{id:3,name:"Business Laptop 15.6\"",cat:"Laptop",price:68500,old:72000,icon:"💻",rating:4.9},
{id:4,name:"Wireless Mechanical Keyboard",cat:"Accessories",price:3250,old:3900,icon:"⌨️",rating:4.6},
{id:5,name:"1080P WiFi CCTV Camera",cat:"CCTV",price:2650,old:3100,icon:"📹",rating:4.7},
{id:6,name:"Wireless Mouse",cat:"Accessories",price:850,old:1100,icon:"🖱️",rating:4.5},
{id:7,name:"65W USB-C Laptop Charger",cat:"Power",price:1950,old:2400,icon:"🔋",rating:4.6},
{id:8,name:"5G Android Smartphone",cat:"Mobile",price:28990,old:31500,icon:"📱",rating:4.8}
];
let cart=JSON.parse(localStorage.getItem("stech-cart")||"[]"), current=[...products];

const money=n=>"৳"+n.toLocaleString("en-BD");
function render(list=current){
 const grid=document.getElementById("productGrid");
 grid.innerHTML=list.map(p=>`<article class="product">
 <div class="product-img">${p.icon}</div><div class="product-body">
 <span class="badge">${p.cat}</span><h3>${p.name}</h3><div class="rating">★ ${p.rating}</div>
 <p><span class="price">${money(p.price)}</span> <span class="old">${money(p.old)}</span></p>
 <div class="product-actions"><button class="details" onclick="details(${p.id})">Details</button><button onclick="addCart(${p.id})">Add to Cart</button></div>
 </div></article>`).join("");
 document.getElementById("resultText").textContent=list.length+" products found";
}
function save(){localStorage.setItem("stech-cart",JSON.stringify(cart));document.getElementById("cartCount").textContent=cart.length}
function addCart(id){const p=products.find(x=>x.id===id);cart.push(p);save();alert(p.name+" added to cart.");}
function openCart(){document.getElementById("cartModal").classList.add("show");renderCart()}
function closeCart(){document.getElementById("cartModal").classList.remove("show")}
function renderCart(){
 const box=document.getElementById("cartItems");
 if(!cart.length){box.innerHTML="<p>Your cart is empty.</p>";document.getElementById("cartTotal").textContent=money(0);return}
 box.innerHTML=cart.map((p,i)=>`<div class="cart-row"><span class="icon">${p.icon}</span><div><strong>${p.name}</strong><br>${money(p.price)}</div><button class="remove" onclick="removeCart(${i})">Remove</button></div>`).join("");
 document.getElementById("cartTotal").textContent=money(cart.reduce((s,p)=>s+p.price,0));
}
function removeCart(i){cart.splice(i,1);save();renderCart()}
function filterCategory(cat){current=products.filter(p=>p.cat===cat);render(current);document.getElementById("products").scrollIntoView({behavior:"smooth"})}
function sortProducts(){const v=document.getElementById("sortSelect").value;current=[...current];if(v==="low")current.sort((a,b)=>a.price-b.price);if(v==="high")current.sort((a,b)=>b.price-a.price);render(current)}
function details(id){const p=products.find(x=>x.id===id);alert(`${p.name}\nCategory: ${p.cat}\nPrice: ${money(p.price)}\nRating: ${p.rating}`)}
function showWishlist(){alert("Wishlist is ready for the next version.");}
function checkout(){if(!cart.length)return alert("Your cart is empty.");alert("Demo checkout: connect this button to your PHP/backend or payment gateway.");}
document.getElementById("searchForm").addEventListener("submit",e=>{e.preventDefault();const q=document.getElementById("searchInput").value.toLowerCase().trim();current=products.filter(p=>(p.name+" "+p.cat).toLowerCase().includes(q));render(current);document.getElementById("products").scrollIntoView({behavior:"smooth"})});
render();save();
