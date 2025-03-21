require("dotenv").config();
const express = require("express");
const nodemailer = require("nodemailer");
const stripe = require("stripe")(process.env.STRIPE_SECRET_KEY); // Utilise la clé secrète Stripe
const cors = require("cors");

const app = express();

// Middleware
app.use(express.json());
app.use(cors()); // Active CORS pour accepter les requêtes depuis n'importe où

// Configurer le transporteur pour les e-mails
const transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST,
    port: process.env.SMTP_PORT,
    secure: true, // true pour le port 465, false pour les autres
    auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS
    }
});

// Route pour envoyer un email via le formulaire de contact
app.post("/send-email", async (req, res) => {
    const { name, email, message } = req.body;

    try {
        await transporter.sendMail({
            from: `"${name}" <${email}>`,
            to: "admin@alkyai.fr",
            subject: "Nouveau message via le formulaire de contact",
            text: message
        });
        res.status(200).json({ success: true, message: "Votre Email a été envoyé !" });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// Route pour créer la session de paiement Stripe
app.post('/create-checkout-session-formation', async (req, res) => {
    try {
        // Créez une session de paiement Stripe
        const session = await stripe.checkout.sessions.create({
            payment_method_types: ['card'],
            line_items: [{
                price: 'price_1R5A6jLiKOMWxvxfVbUtbHDE', // Utilisez votre ID de prix ici
                quantity: 1,
            }],
            mode: 'payment',
            success_url: 'https://alkyai.fr/success-formation.html', // URL de succès
            cancel_url: 'https://alkyai.fr/cancel-formation.html',  // URL d'annulation
        });

        // Renvoyez l'ID de la session au client
        res.json({ id: session.id });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});
// Route pour créer la session de paiement Stripe pour la formation
app.post('/create-checkout-session-formation', async (req, res) => {
    try {
        // Créez une session de paiement Stripe
        const session = await stripe.checkout.sessions.create({
            payment_method_types: ['card'],
            line_items: [{
                price: 'price_1R5A6jLiKOMWxvxfVbUtbHDE', // ID de prix pour 90 €
                quantity: 1,
            }],
            mode: 'payment',
            success_url: 'https://alkyai.fr/success-formation.html', // URL de succès
            cancel_url: 'https://alkyai.fr/cancel-formation.html',  // URL d'annulation
        });

        // Renvoyez l'ID de la session au client
        res.json({ id: session.id });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});
// Démarre le serveur
const PORT = process.env.PORT || 10000; // Utilise le port spécifié dans l'environnement ou 10000
app.listen(PORT, () => console.log(`✅ Serveur en ligne sur le port ${PORT}`));