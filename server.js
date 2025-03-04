const express = require('express');
const cors = require('cors');
require('dotenv').config(); // Charger les variables d'environnement
console.log("🔑 Clé Stripe : ", process.env.STRIPE_SECRET_KEY ? "✅ Chargée" : "❌ Manquante");

const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY);
const path = require('path');

console.log("Stripe Secret Key:", process.env.STRIPE_SECRET_KEY ? "Détectée" : "Non détectée");

const app = express();
app.use(express.json());
app.use(cors());

// 📌 Servir les fichiers HTML/CSS/JS
app.use(express.static(__dirname));

// 📌 Route principale (index.html)
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// 📌 Route Stripe pour créer une session de paiement
app.post('/create-checkout-session', async (req, res) => {
    try {
        const { amount } = req.body;
        console.log("💰 Montant reçu :", amount);

        const session = await stripe.checkout.sessions.create({
            payment_method_types: ['card'],
            line_items: [
                {
                    price_data: {
                        currency: 'eur',
                        product_data: {
                            name: 'Investissement AlkyBin',
                        },
                        unit_amount: parseInt(amount), // Montant en centimes
                    },
                    quantity: 1,
                },
            ],
            mode: 'payment',
            success_url: 'https://alkyai.railway.internal/success.html',
            cancel_url: 'https://alkyai.railway.internal/cancel.html',
        });

        res.json({ id: session.id });
    } catch (error) {
        console.error(error);
        res.status(500).json({ error: 'Erreur lors de la création de la session' });
    }
});

// 📌 Lancer le serveur
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`✅ Serveur démarré sur http://localhost:${PORT}`));
