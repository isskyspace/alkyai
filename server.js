const express = require('express');
const cors = require('cors');
require('dotenv').config(); 
const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY);
const path = require('path');

const app = express();
app.use(express.json());
app.use(cors({ origin: '*' })); // 🔥 Ajout pour éviter les erreurs CORS

console.log("🔑 Clé Stripe chargée :", process.env.STRIPE_SECRET_KEY ? "✅ Oui" : "❌ Non");

// 📌 Servir les fichiers HTML/CSS/JS
app.use(express.static(__dirname));

// 📌 Page d'accueil
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// 📌 Route pour créer une session de paiement
app.post('/create-checkout-session', async (req, res) => {
    try {
        let { amount } = req.body;
        console.log("💰 Montant reçu :", amount);

        if (!amount || isNaN(amount)) {
            return res.status(400).json({ error: "Montant invalide" });
        }

        amount = parseFloat(amount) * 100; // 💡 Convertir € → centimes
        console.log("💵 Montant en centimes :", amount);

        const session = await stripe.checkout.sessions.create({
            payment_method_types: ['card'],
            line_items: [
                {
                    price_data: {
                        currency: 'eur',
                        product_data: {
                            name: 'Investissement AlkyBin',
                        },
                        unit_amount: Math.round(amount), // 🔥 Bien s’assurer d’un entier
                    },
                    quantity: 1,
                },
            ],
            mode: 'payment',
            success_url: 'https://alkyai.railway.app/success.html', // 🔥 Corrigé
            cancel_url: 'https://alkyai.railway.app/cancel.html', // 🔥 Corrigé
        });

        console.log("✅ Session Stripe créée :", session.id);
        res.json({ id: session.id });

    } catch (error) {
        console.error("❌ Erreur Stripe :", error);
        res.status(500).json({ error: 'Erreur lors de la création de la session' });
    }
});

// 📌 Lancer le serveur
const PORT = process.env.PORT || 8080;
app.listen(PORT, () => console.log(`✅ Serveur sur http://localhost:${PORT}`));
