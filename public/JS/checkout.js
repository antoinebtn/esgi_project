// Initialize Stripe.js
const stripe = Stripe('pk_test_51OMsYYJNd8Md8wujqbrQKWGmvlezz50ljy8wDLv2Yw33CiKjaMq6qVzscSfxSVC9g7bBrm3ByioJIKCu00fLYEL500bZH0CYNB', {
});
// Fetch Checkout Session and retrieve the client secret
initialize();
// Fetch Checkout Session and retrieve the client secret
async function initialize() {
    // Récupérer l'élément avec l'ID "checkout"
    var checkoutElement = document.getElementById('checkout');

// Récupérer la valeur de l'attribut data-stripe-client-secret
    var clientSecret = checkoutElement.getAttribute('data-stripe-client-secret');

    const checkout = await stripe.initEmbeddedCheckout({
        clientSecret,
    });

    // Mount Checkout
    checkout.mount('#checkout');
}