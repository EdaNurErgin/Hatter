const searchForm = document.querySelector(".search-form");
const cartItem = document.querySelector(".cart-items-container");

const navbar = document.querySelector(".navbar");

// ulasma kontrolu
// console.log(searchForm);

//! buttons
const searchBtn = document.querySelector("#search-btn");
const cartBtn = document.querySelector("#cart-btn");
const menuBtn = document.querySelector("#menu-btn");

console.log(menuBtn);  // Buton bulundu mu?
console.log(navbar);   // Navbar bulundu mu?


// arama butonuna bastıgında bar cıkmasını saglayacak cunku class a active verdiginde bar ortaya cıkıyordu tıkladıgında kapanmasını istiyosan toggle ver sadece cıkmasını istiyosan add ver
searchBtn.addEventListener("click",function(){
    searchForm.classList.toggle("active");
    document.addEventListener("click",function(e){
        if(!e.composedPath().includes(searchBtn) &&
        // FORM KUTUSUNDA BASTIGINDA KAPANMASINI ENGELLEYEN KOD SATIRI
        !e.composedPath().includes(searchForm)){
            searchForm.classList.remove("active");
        }
    });


});


cartBtn.addEventListener("click",function(){
    cartItem.classList.toggle("active");
    document.addEventListener("click",function(e){
        if(!e.composedPath().includes(cartBtn) &&
        // FORM KUTUSUNDA BASTIGINDA KAPANMASINI ENGELLEYEN KOD SATIRI
        !e.composedPath().includes(cartItem)){
            cartItem.classList.remove("active");
        }
    });


});


menuBtn.addEventListener("click",function(){
    navbar.classList.toggle("active");
    document.addEventListener("click",function(e){
        if(!e.composedPath().includes(menuBtn) &&
        // FORM KUTUSUNDA BASTIGINDA KAPANMASINI ENGELLEYEN KOD SATIRI
        !e.composedPath().includes(navbar)){
            navbar.classList.remove("active");
        }
    });


});






document.getElementById("menu-btn").addEventListener("click", function () {
    console.log("Menü butonuna tıklandı!");
});

window.addEventListener("resize", function () {
    if (window.innerWidth > 768) { 
        document.querySelector(".navbar").classList.remove("active"); 
    }
});




